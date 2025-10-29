<?php

namespace App\Http\Controllers\Api\V1\Checkout;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\UserVerify;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Laravel\Passport\Token;
use Illuminate\Http\Request;
use App\Models\DeliveryCharge;
use App\Services\OrderService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\RefreshToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Traits\PaginatedResourceTrait;
use App\Services\DeliveryChargeService;
use App\Http\Resources\Order\OrderResource;
use App\Notifications\EmailVerifyNotifyMail;
use App\Http\Resources\Order\OrderHistoriesResource;

class CheckoutController
{
    use ApiResponse, PaginatedResourceTrait;
    public $promotionService;

    protected $orderService;
    protected $deliveryChargeService;
    public function __construct(OrderService $orderService, DeliveryChargeService $deliveryChargeService)
    {
        $this->orderService = $orderService;
        $this->deliveryChargeService   = $deliveryChargeService;

    }



    public function checkoutConfirm(Request $request)
    {
        $this->validateCheckoutRequest($request);
        // dd($request->all());
        $uuid = $request->uuid;

        $allCarts = Cart::query() // eager load product
        ->where('uuid', $uuid)
        ->whereHas('product', function ($query) {
            $query->where('status', STATUS_ACTIVE)
                ->where('total_stock_quantity', '>', 0);
        })
        ->get();
        // dd($allCarts);
        if(empty($allCarts) || count($allCarts) == 0){
            return $this->error('Cart is empty');
        }
        $sale_items = $allCarts->map(function ($cart) {
            return [
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity
            ];
        })->toArray();

        try {
            DB::beginTransaction();
            $authUser = auth()->user();

            // $deliveryCharge = DeliveryCharge::findOrFail($request->delivery_charge_id);

            $deliveryCharge = $this->deliveryChargeService->getDeliveryCharge(['id','title','cost']);

            $deliveryCost = $deliveryCharge && !empty($deliveryCharge) ? $deliveryCharge->cost : 0;

            $orderBaseData = $this->prepareBaseOrderData($authUser, $request, $deliveryCost);

            [$saleItems, $subTotal, $taxAmount, $canSplitSale] = $this->processSaleItems($sale_items, $authUser);


            $total = $subTotal + $taxAmount + $deliveryCost;
            $orderData = array_merge($orderBaseData, [
                'sub_total' => ceil($subTotal),
                'tax_amount' => $taxAmount,
                'total' => ceil($total),
                'is_split_sale' => $canSplitSale,
            ]);
            // dd($saleItems, $subTotal, $taxAmount, $canSplitSale, $orderData);

            // You can create the order and attach items here...
            $order = Order::create($orderData);
            $order->order_items()->createMany($saleItems);
            $staticStatus = Order::STATUS_PENDING;
                //---------------------- Order Status Create ----------------------
            $customStatus = [
                'title' => ucwords(str_replace('_', ' ',  $staticStatus)),
                'message' =>  'Your Order Successfully Created. Invoice id: '.$order->invoice_no,
                'order_type'    => 'success',
                'status' => $staticStatus,
                'user_id' => auth()->user()->id,
                // 'visit_url' => route('order.status.change', $sale->id),
            ];
            $this->orderService->orderStatusCreate($order, $customStatus);

            $customStatusText = ucwords(str_replace('_', ' ', $staticStatus));
            //all admin notify
            // $this->orderService->allAdminNotify($order);
            $this->orderService->notifyAdmins($order, $customStatusText);

            //existing cart remove
            $allCarts->each(function ($cart) {
                $cart->delete();
            });

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Checkout successful', 'order_id' => $order->id]);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }



    }

    private function validateCheckoutRequest(Request $request): void
    {
        $request->validate([
            // 'delivery_charge_id' => 'required|exists:delivery_charges,id',
            'billing_info' => 'required|array',
            'shipping_info' => 'required|array',
            'uuid' => 'required|exists:carts,uuid',
            // 'sale_items' => 'required|array|min:1',
            // 'sale_items.*.product_id' => 'required|integer|exists:products,id',
            // 'sale_items.*.quantity' => 'required|numeric|min:1',
        ]);
    }

    private function prepareBaseOrderData($user, Request $request, $cost): array
    {

        return [
            'invoice_no' => $this->orderService->invoiceNoGenerate(),
            'date' => now(),
            'order_for_id' => $user->id,
            'order_for' => $user->type,
            'platform' => Order::PLATFORM_MOBILE,
            'billing_info' => $request->billing_info,
            'shipping_info' => $request->shipping_info,
            'delivery_cost' => $cost,
            'delivery_charge_id' => $request->delivery_charge_id,
            'order_status' => Order::STATUS_PENDING,
            'sub_total' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'is_split_sale' => 0,
        ];
    }

    private function processSaleItems(array $items, $user): array
    {
        $saleItems = [];
        $subTotal = 0;
        $taxAmount = 0;
        $canSplitSale = false;
        $now = Carbon::now();
        $available_for = $user->type;

        foreach ($items as $item) {
            $quantity = $item['quantity'];
            $product = Product::whereHas('warehouse_stock', function ($q) use ($quantity) {
                $q->where('stock_quantity', '>', $quantity);
            })
            ->with(['latest_promotion_item' => function ($query) use ($available_for , $now) {
                    return $query->with(['promotion'])
                    ->whereHas('promotion', function ($query) use ($available_for, $now) {
                        $query->where('target_type', $available_for)
                        ->whereDate('start_date', '<=', $now)
                        ->whereDate('end_date', '>=', $now);
                    });
                }])
            ->active()
            ->find($item['product_id']);

            if (!$product) {
                throw new \Exception("Product not found or insufficient stock or product is inactive.");
            }


            $discount = (float)$product?->latest_promotion_item?->promotion->offer_value ?? null;
            $discount_type = $product?->latest_promotion_item?->promotion->offer_type ?? null; //fixed or percent

            $price = $user->type == User::TYPE_REGULAR_USER ? $product->sale_price : $product->restaurant_sale_price;
            $lineSubTotal = $price * $quantity;



            if(!empty($discount) && !empty($discount_type)){
                if($discount_type == 'fixed'){
                    $lineSubTotal = $lineSubTotal - $discount;
                }else{
                    $lineSubTotal = $lineSubTotal - ($lineSubTotal * ($discount / 100));
                }
            }

            // dd($lineSubTotal, $price, $quantity, $discount, $discount_type);

            // dd($lineSubTotal, $discount, $discount_type);
            $lineSubTotal = ceil($lineSubTotal);
            $subTotal += ($lineSubTotal);

            if (!empty($product->taxes) && $product->taxes->has_tax == 1) {
                $productTaxAmount = $product->taxes->tax_amount ?? 0;
                $taxAmount += $lineSubTotal * ($productTaxAmount / 100);
                $taxAmount = ceil($taxAmount);
            }

            // dd($taxAmount);

            if ($product->is_split_sale == 1) {
                $canSplitSale = true;
            }

            $saleItems[] = [
                'product_id' => $product->id,
                'warehouse_id' => $product->warehouse_stock->warehouse_id,
                'warehouse_stock_id' => $product->warehouse_stock->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'product_barcode' => $product->barcode,
                'quantity' => $quantity,
                'price' => ceil($price),
                'sub_total' => $lineSubTotal,
                'discount' => $discount,
                'discount_type' => $discount_type,
            ];
        }

        return [$saleItems, $subTotal, $taxAmount, $canSplitSale];
    }




}
