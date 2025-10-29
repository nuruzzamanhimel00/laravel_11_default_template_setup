<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id ?? '',
            'product_id' => $this->product_id ?? '',
            'warehouse_id' => $this->warehouse_id ?? null,
            'warehouse_stock_id' => $this->warehouse_stock_id ?? null,
            'product_name' => $this->product_name ?? '',
            'product_sku' => $this->product_sku ?? '',
            'product_barcode' => $this->product_barcode ?? '',
            'quantity' => $this->quantity ?? '',
            'price' => addCurrency($this->price) ?? '',
            'discount' => addCurrency($this->discount) ?? '',
            'discount_type' => $this->discount_type ?? '',
            'sub_total' => addCurrency($this->sub_total) ?? '',

            // 'product' => $this->whenLoaded('product'),
            'product' => new ProductResource($this->whenLoaded('product')),
            'warehouse' => $this->whenLoaded('warehouse'),


        ];
    }
}
