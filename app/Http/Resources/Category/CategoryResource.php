<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return [
        //     'id' => $this->id,
        //     'name' => $this->name ?? '', // Fallback to empty string if null
        //     'slug' => $this->slug ?? '', // Fallback to empty string if null
            // 'image' => $this->image ?? null, // Return null if image is not set
            // 'image_url' => $this->image_url ?? null, // Use whenLoaded for relationships or computed fields
        //     'description' => $this->description ?? '', // Fallback to empty string if null
        //     'status' => $this->status ?? '', // Fallback to empty string if null
        //     'parent' => new self($this->whenLoaded('parent')), // Recursive parent category
        // ];
        // $data =  [
        //     'id' => $this->id,
        //     'name' => $this->name ?? '', // Fallback to empty string if null
        //     // 'slug' => $this->slug ?? '', // Fallback to empty string if null
        //     // 'image' => $this->image ?? null, // Return null if image is not set
        //     // 'image_url' => $this->image_url ?? null, // Use whenLoaded for relationships or computed fields
        //     // 'description' => $this->description ?? '', // Fallback to empty string if null
        //     // 'status' => $this->status ?? '', // Fallback to empty string if null
        //     // 'parent' => new self($this->whenLoaded('parent')), // Recursive parent category
        // ];
        // if($this->slug){
        //     $data['slug'] =  $this->slug ?? '';
        // }
        // if($this->description){
        //     $data['description'] =  $this->description ?? '';
        // }
        // if($this->status){
        //     $data['status'] =  $this->status ?? '';
        // }
        // if($this->whenLoaded('parent')){
        //     $data['parent'] =   new self($this->whenLoaded('parent')),
        // }
        // if($this->image){
        //     $data['image'] =  $this->image ?? '';
        //     $data['image_url'] =  $this->image_url ?? '';
        // }
        $data = [
            'id' => $this->id,
            'name' => $this->name ?? '',
            'image' => $this->image ?? null, // Return null if image is not set
            'image_url' => $this->image_url ?? null, // Use whenLoaded for relationships or computed fields

        ];

        if (!is_null($this->slug)) {
            $data['slug'] = $this->slug;
        }

        if (!is_null($this->description)) {
            $data['description'] = $this->description;
        }

        if (!is_null($this->status)) {
            $data['status'] = $this->status;
        }

        // if ($this->relationLoaded('parent') && $this->parent) {
        //     $data['parent'] = new self($this->parent);
        // }
        if ($this->relationLoaded('childs') && $this->childs && count($this->childs) > 0) {
            $data['childs'] = CategoryResource::collection($this->childs);
        }



        return $data;
    }
}
