<?php

namespace App\Http\Resources\Product;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Brand\BrandResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Promotion\PromotionItemsResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $available_for = request()->available_for ?? null;

        $price = $this->sale_price;
        if ($available_for && !is_null($available_for)) {
            $price = $available_for === User::TYPE_RESTAURANT
                ? $this->restaurant_sale_price
                : $this->sale_price;
        }

        $resources =  [
            'id' => $this->id,
            'category_id' => $this->category_id ?? '',
            'brand_id' => $this->brand_id ?? '',
            'product_unit_id' => $this->product_unit_id ?? '',
            'name' => $this->name ?? '',
            'image_url' => $this->image_url ?? '',
            'sku' => $this->sku ?? '',
            'barcode' => $this->barcode ?? '',
            'barcode_image_url' => $this->barcode_image_url ?? '',
            'price' => $price ?? '',
            'status' => $this->status ?? '',
            'total_stock_quantity' => $this->total_stock_quantity ?? '',
            'low_stock_alert' => $this->low_stock_alert ?? '',
            'available_for' => $this->available_for ?? '',
            'is_split_sale' => $this->is_split_sale ?? '',
            'taxes' => $this->taxes ?? '',
            'meta' => $this->meta ?? '',
            'rating' => $this->rating ?? '',
            'created_at' => $this->created_at ?? '',
            'warehouse_stock' =>($this->whenLoaded('warehouse_stock')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'product_unit' =>$this->whenLoaded('productUnit'),
            'product_meta' =>$this->whenLoaded('productMeta') ,
            // 'average_rating' => $this->reviews->count() > 0 ? formatNumberSmart($this->reviews->avg('rating')) : 0,
            // 'total_reviews' => $this->reviews->count() ?? 0
            'latest_promotion_item' => new PromotionItemsResource($this->whenLoaded('latest_promotion_item'))
        ];


        return $resources;
    }
}
