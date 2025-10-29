<?php

namespace App\Http\Controllers\Api\V1\Cart;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Traits\PaginatedResourceTrait;
use App\Services\DeliveryChargeService;
use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Category\CategoryResource;

class CartController extends Controller
{
    use ApiResponse, PaginatedResourceTrait;
    public $brandService;
    public $deliveryChargeService;
    public function __construct(BrandService $brandService, DeliveryChargeService $deliveryChargeService)
    {
        $this->brandService = $brandService;
        $this->deliveryChargeService   = $deliveryChargeService;
    }



    public function getCart(Request $request)
    {
        $uuid = $request->uuid;

        if (empty($uuid)) {
            return $this->cartNotFoundResponse();
        }

        $allCarts = Cart::with('product')
            ->where('uuid', $uuid)
            ->whereHas('product', function ($query) {
                $query->where('status', STATUS_ACTIVE)
                    ->where('total_stock_quantity', '>', 0);
            })
            ->get();

        $items = $allCarts->map(function ($cart) {
            return $this->prepareCartData($cart->id, $cart->quantity, $cart->product_id);
        })->filter()->values(); // Remove nulls & reindex

        if ($items->isEmpty()) {
            return $this->cartNotFoundResponse();
        }

        // Use float calculations internally
        $subTotal = $items->sum(fn($item) => (float) $item['sub_total'] * (int) $item['quantity']);
        $totalTaxPrice = $items->sum(fn($item) => (float) $item['tax_price']);

        $deliveryCharge = $this->deliveryChargeService->getDeliveryCharge(['id', 'title', 'cost']);
        $deliveryCost = $deliveryCharge?->cost ?? 0;
        $total = $subTotal + $deliveryCost;

        // Format with currency symbol
        $items = $items->map(function ($item) {
            foreach (['price', 'promotion_price', 'sub_total', 'old_price', 'total_price', 'tax_price'] as $field) {
                $item[$field] = addCurrency($item[$field]);
            }
            return $item;
        })->toArray();

        return response()->json([
            'items'            => $items,
            'sub_total'        => addCurrency($subTotal),
            'total'            => addCurrency($total),
            'delivery_cost'    => addCurrency($deliveryCost),
            'total_tax_price'  => addCurrency($totalTaxPrice),
        ]);
    }
    private function cartNotFoundResponse()
    {
        return response()->json([
            'message'           => 'Item not found in cart!',
            'items'             => [],
            'sub_total'         => addCurrency(0),
            'total'             => addCurrency(0),
            'delivery_cost'     => addCurrency(0),
            'total_tax_price'   => addCurrency(0),
        ], 200);
    }



    public function prepareCartData($cartId,$cartQuantity,$productId){

        $availableFor = auth('api')->check() ? auth('api')->user()->type : null; // 'Restaurant' or 'Customer'

        $product = Product::where('id', $productId)
            ->where('total_stock_quantity', '>', 0)
            ->where('status', STATUS_ACTIVE)
            ->select([
                'id',
                'name',
                'image',
                'category_id',
                'product_unit_id',
                'total_stock_quantity',
                'sale_price',
                'restaurant_sale_price',
                'available_for',
                'taxes',
                'meta'
            ])
            ->when($availableFor, function ($query) use ($availableFor) {
                $query->where(function ($q) use ($availableFor) {
                    $q->where('available_for', 'Both')
                    ->orWhere('available_for', $availableFor);
                });
            })
            ->firstOrFail();

                // dd($product);
        if(!$product){
            return ;
        }


        $now = Carbon::now();
        $product = $product
         ->load([
            'latest_promotion_item'=> function ($q) use ($availableFor, $now) {
                $q->with(['promotion'=>function($q){
                    $q->select('id','title','offer_type','offer_value');
                }, 'category'])
                ->whereHas('promotion', function ($query) use ($availableFor, $now) {
                    $query->where('target_type', $availableFor)
                            ->whereDate('start_date', '<=', $now)
                            ->whereDate('end_date', '>=', $now);
                });
            },
            'category'=> function ($q) {
                $q->select('id', 'name');
            },
            'productUnit'=> function ($q) {
                $q->select('id', 'name', 'symbol', 'type');
            },
            'productMeta'=>function ($q) {
                $q->select('id', 'product_id', 'unit_value','notes');
            }
        ]);




        $priceData = calculatePayablePrice($product, $availableFor );
        // dd($priceData);
        $data = [
            'product_id' => $product->id,
            'cart_id' => $cartId,
            'product_name' => $product->name,
            'image_url' => $product->image_url,
            'notes' => $product->productMeta->notes ?? '',
            'price' => $priceData['price'],
            'tax_price' => $priceData['tax_price'],
            'promotion_price' => $priceData['promotion_price'],
            'sub_total' => $priceData['payable_price'],
            'old_price' =>  $priceData['old_price'],
            'quantity' => $cartQuantity,
            'total_price' => ($priceData['payable_price'] * $cartQuantity),
            // 'taxes' => !empty($product->taxes) ? collect($product->taxes)->toArray(): [],
            'rating' => $product->rating,
            'total_stock_quantity' => $product->total_stock_quantity,
            'product_unit' => !is_null($product->productUnit) ? $product->productUnit->toArray() : null,
            'product_meta' => !is_null($product->productMeta) ? $product->productMeta->toArray() : null,
            'category' => !is_null($product->category) ? $product->category->toArray() : null,
            'promotion' => !is_null($product->latest_promotion_item) ? (
                $product->latest_promotion_item->promotion->toArray() ?? null
            ) : null,
        ];
        // dd('data',$data);
        return $data;
        // dd('product',$product);

    }




    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $quantity = $request->quantity ?? 1;
        $uuid = $request->uuid ?? generateUuid();

        try {
            DB::beginTransaction();

            $product = $this->getAvailableProduct($request->product_id);

            if (!$product) {
                return response()->json(['message' => 'Product not available or out of stock.'], 200);
            }

            $cartItem = Cart::where('uuid', $uuid)
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $quantity;
                if ($newQuantity > $product->total_stock_quantity) {
                    return response()->json(['message' => 'Out of stock!'], 200);
                }

                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                if ($quantity > $product->total_stock_quantity) {
                    return response()->json(['message' => 'Out of stock!'], 200);
                }

                $cartItem = Cart::create([
                    'uuid'       => $uuid,
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Item added to cart',
                'cart'    => $cartItem,
                'uuid'    => $uuid,
                'success' => true,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    private function getAvailableProduct($productId)
    {
        return Product::where('id', $productId)
            ->where('total_stock_quantity', '>', 0)
            ->where('status', STATUS_ACTIVE)
            ->select([
                'id',
                'name',
                'image',
                'category_id',
                'product_unit_id',
                'total_stock_quantity',
                'sale_price',
                'restaurant_sale_price',
                'available_for',
                'taxes',
                'meta'
            ])
            ->first();
    }



    public function minusToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);



        // $auth = auth()->user();
        $uuid = $request->uuid ;
        if(!$uuid || $uuid == null){
            return response()->json([
                'message'           => 'Item not found in cart!',
                'items'             => [],
                'sub_total'         => number_format(0, 2, '.', ''), // Ensures 2 decimals as string
                'total'             => 0,
                'delivery_cost'     => 0,
                'total_tax_price'   => 0,
            ], 200);
        }


        try {
            DB::beginTransaction();

            $product = Product::where('id', $request->product_id)
                // ->where('total_stock_quantity', '>', 0)
                ->where('status', STATUS_ACTIVE)
                ->select([
                    'id',
                    'name',
                    'image',
                    'category_id',
                    'product_unit_id',
                    'total_stock_quantity',
                    'sale_price',
                    'restaurant_sale_price',
                    'available_for',
                    'taxes',
                    'meta'
                ])
                ->firstOrFail();

            $cart = Cart::where('uuid', $uuid)
                ->where('product_id', $request->product_id)
                ->first();

            if (!$cart) {
                return response()->json(['message' => 'Item not found in cart!'], 200);
            }

            if ($request->quantity > $cart->quantity) {
                return response()->json(['message' => 'Invalid quantity requested!'], 400);
            }

            if ($request->quantity > $product->total_stock_quantity) {
                return response()->json(['message' => 'Requested quantity exceeds stock!'], 400);
            }

            // Decrease the quantity
            $cart->quantity -= $request->quantity;

            if ($cart->quantity <= 0) {
                $cart->delete(); // Remove the cart item entirely if quantity becomes 0
            } else {
                $cart->save();
            }

            DB::commit();

            return response()->json(['message' => 'Item quantity updated successfully.','cart' => $cart,'success' => true,'uuid' => $uuid]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }





    public function removeFromCart($id)
    {
        //     if (!Auth::check()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Please login first'
        //     ]);
        // }

        $cart = Cart::find($id);
        if(!$cart){
            return response()->json(['message' => 'Item not found!'], 200);
        }
        $cart->delete();
        return response()->json(['message' => 'Item removed!']);
    }




}
