<?php

namespace App\Http\Resources\Review;

use Illuminate\Http\Request;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Review\ReviewImagesResource;

class ReviewResource extends JsonResource
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
            'rating' => $this->rating ?? '', // Fallback to empty string if null
            'message' => $this->message ?? null, // Return null if image is not set
            'customer' => new UserResource($this->whenLoaded('customer')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'images' => ReviewImagesResource::collection($this->whenLoaded('images')),
        ];
    }
}
