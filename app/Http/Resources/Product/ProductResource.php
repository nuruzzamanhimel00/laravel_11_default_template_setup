<?php

namespace App\Http\Resources\Product;

use App\Models\User;
use App\Models\Promotion;
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
        $availableFor = auth('api')->check() ? auth('api')->user()->type : null;

        $priceData = calculatePayablePrice($this, $availableFor );


        $data = [
            'id' => $this->id,
            'price' => addCurrency($priceData['price']),
            'tax_price' => addCurrency($priceData['tax_price']),
            'promotion_price' => addCurrency($priceData['promotion_price']),
            'sub_total' => addCurrency($priceData['payable_price']),
            // 'image' => $this->image ?? null,
            'image_url' => $this->image_url ?? null,
            'details_image_url' => $this->details_image_url ?? null,
        ];

        // if (!is_null($this->category_id)) {
        //     $data['category_id'] = $this->category_id;
        // }

        // if (!is_null($this->brand_id)) {
        //     $data['brand_id'] = $this->brand_id;
        // }

        // if (!is_null($this->product_unit_id)) {
        //     $data['product_unit_id'] = $this->product_unit_id;
        // }

        if (!is_null($this->name)) {
            $data['name'] = $this->name;
        }

        // if (!is_null($this->image_url)) {
        //     $data['image_url'] = $this->image_url;
        // }

        if (!is_null($this->sku)) {
            $data['sku'] = $this->sku;
        }

        // if (!is_null($this->barcode)) {
        //     $data['barcode'] = $this->barcode;
        // }

        // if (!is_null($this->barcode_image_url)) {
        //     $data['barcode_image_url'] = $this->barcode_image_url;
        // }

        // if (!is_null($this->status)) {
        //     $data['status'] = $this->status;
        // }

        if (!is_null($this->total_stock_quantity)) {
            $data['total_stock_quantity'] = $this->total_stock_quantity;
        }

        // if (!is_null($this->low_stock_alert)) {
        //     $data['low_stock_alert'] = $this->low_stock_alert;
        // }

        if (!is_null($this->available_for)) {
            $data['available_for'] = $this->available_for;
        }

        // if (!is_null($this->is_split_sale)) {
        //     $data['is_split_sale'] = $this->is_split_sale;
        // }

        // if (!is_null($this->taxes)) {
        //     $data['taxes'] = $this->taxes;
        // }

        if (!is_null($this->meta)) {
            $data['meta'] = $this->meta;
        }

        if (!is_null($this->rating)) {
            $data['rating'] = $this->rating;
        }

        if (!is_null($this->created_at)) {
            $data['created_at'] = $this->created_at_human;
        }

        // if ($this->relationLoaded('warehouse_stock')) {
        //     $data['warehouse_stock'] =$this->whenLoaded('warehouse_stock');
        // }

        if ($this->relationLoaded('category') && $this->category) {
            $data['category'] =new CategoryResource($this->whenLoaded('category'));
        }

        if ($this->relationLoaded('brand') && $this->brand) {
            $data['brand'] = new BrandResource($this->whenLoaded('brand'));
        }

        if ($this->relationLoaded('productUnit') && $this->productUnit) {
            $data['product_unit'] = $this->whenLoaded('productUnit');
        }

        if ($this->relationLoaded('productMeta') && $this->productMeta) {
            $data['product_meta'] =$this->whenLoaded('productMeta');
        }
        if ($this->relationLoaded('product_tags') && $this->product_tags) {
            $data['product_tags'] = $this->product_tags->count() > 0 ? $this->product_tags->map->only(['id', 'name']): collect([]);
        }

        $data['promotion_text'] = '';
        $data['old_price'] = '';
        if ($this->relationLoaded('latest_promotion_item') && $this->latest_promotion_item) {
            // $data['latest_promotion_item'] =new PromotionItemsResource($this->whenLoaded('latest_promotion_item'));

            $offer_value = formatNumber($this->latest_promotion_item?->promotion->offer_value);
            // dd($offer_value);
            $data['promotion_text'] = $this->latest_promotion_item?->promotion->offer_type == 'percent' ? formatNumberSmart($offer_value).'% off' : '৳ '.formatNumberSmart($offer_value).' off';
            // dd($this->latest_promotion_item);

            $data['old_price'] = $priceData['old_price'] ?  addCurrency($priceData['old_price']) : '' ;

        }

        return $data;
    }

}
