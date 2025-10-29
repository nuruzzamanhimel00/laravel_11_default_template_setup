<?php

namespace App\Http\Resources\Brand;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            'name' => $this->name ?? '', // Fallback to empty string if null
            'image' => $this->image ?? null, // Return null if image is not set
            'image_url' => $this->image_url ?? null, // Use whenLoaded for relationships or computed fields
            'status' => $this->status ?? '', // Fallback to empty string if null
        ];
    }
}
