<?php

namespace App\Http\Resources\Promotion;

use Illuminate\Http\Request;
use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Category\CategoryResource;

class PromotionItemsResource extends JsonResource
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
            'category' =>  new CategoryResource($this->whenLoaded('category')),
            'product' => new  ProductResource($this->whenLoaded('product')),
            'promotion' => new  PromotionResource($this->whenLoaded('promotion')),



        ];
    }
}
