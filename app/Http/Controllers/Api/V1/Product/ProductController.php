<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Traits\PaginatedResourceTrait;
use App\Services\Product\ProductService;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Category\CategoryResource;

class ProductController extends Controller
{
    use ApiResponse, PaginatedResourceTrait;
    public $productService;
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    //get all active pages
    public function index()
    {
        // dd(auth('api')->check());
        try {

            $products = $this->productService->getAll(
                [
                    'id', 'category_id', 'brand_id', 'product_unit_id', 'name', 'image','details_image',
                    'purchase_price', 'sale_price', 'restaurant_sale_price',
                     'total_stock_quantity', 'available_for',
                     'taxes', 'meta', 'created_at'
                ],['latest_promotion_item','category','brand','productUnit','productMeta']
            );
            // dd($products);
            $resource =  $this->paginatedResponse($products, ProductResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function show($id)
    {
        try {
            $product = $this->productService->getDetails($id)->load(['latest_promotion_item']);

            if (!$product) {
                return $this->error('Product not found', 404);
            }

            // $availableFor = request()->input('available_for');
            // $price = $availableFor === User::TYPE_RESTAURANT
            //     ? $product->restaurant_sale_price
            //     : $product->sale_price;
            $availableFor = auth('api')->check() ? auth('api')->user()->type : null;

            $priceData = calculatePayablePrice($product, $availableFor );




            $productData = [
                'id' => $product->id,
                'name' => $product->name,
                'image_url' => $product->image_url,
                'details_image_url' => $product->details_image_url,
                'price' => addCurrency($priceData['price']),
                'tax_price' => addCurrency($priceData['tax_price']),
                'promotion_price' => addCurrency($priceData['promotion_price']),
                'sub_total' => addCurrency($priceData['payable_price']),
                'old_price' => addCurrency($priceData['old_price']),
                // 'taxes' => $product->taxes,
                'rating' => $product->rating,
                'total_stock_quantity' => $product->total_stock_quantity,
                'product_unit' => $product->productUnit,
                'notes' => optional($product->productMeta)->notes,
                // 'latest_promotion_item' => $product->latest_promotion_item,
                'category' => [
                    'id' => optional($product->category)->id,
                    'name' => optional($product->category)->name,
                ],
                'brand' => [
                    'id' => optional($product->brand)->id,
                    'name' => optional($product->brand)->name,
                ],
                'promotion_text' => '',
                'created_at' => $product->created_at_human,
            ];
            if ($product->latest_promotion_item) {
                $offer_value = formatNumber($product->latest_promotion_item?->promotion->offer_value);
                $productData['promotion_text'] = $product->latest_promotion_item?->promotion->offer_type == 'percent' ? $offer_value.'% off' : '৳ '.$offer_value.' off';

            }

            $relatedItems = $this->productService->relatedItems(
                $id,
                ['id', 'name', 'image', 'purchase_price', 'sale_price', 'restaurant_sale_price', 'taxes', 'total_stock_quantity','details_image'],
                ['latest_promotion_item']
            );

            return $this->success([
                'product' => $productData,
                'related_items' => $this->paginatedResponse($relatedItems, ProductResource::class)
            ], 'Product details loaded successfully', 200);

        } catch (\Exception $e) {
            logger()->error('Product show error: ' . $e->getMessage());
            return $this->error('Something went wrong', 500);
        }
    }

    public function getRelatedItems($id)
    {

        try {

            $products = $this->productService->relatedItems($id,['id','name','image','purchase_price','sale_price','restaurant_sale_price','taxes','total_stock_quantity','details_image'],['latest_promotion_item']);
            // dd($products);
            $resource = $this->paginatedResponse($products, ProductResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function bestSellingProducts()
    {
        try {
            $products = $this->productService->getBestSellingProducts(['id','name','image','purchase_price','sale_price','restaurant_sale_price','taxes','total_stock_quantity','details_image'],['latest_promotion_item']);
            // dd($products);
            $resource = $this->paginatedResponse($products, ProductResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function newArrivalProducts()
    {
        try {
            $products = $this->productService->getNewArrivalProducts(['id','name','image','purchase_price','sale_price','restaurant_sale_price','taxes','total_stock_quantity','details_image'],['latest_promotion_item']);
            // return ($products);
            $resource = $this->paginatedResponse($products, ProductResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }


}
