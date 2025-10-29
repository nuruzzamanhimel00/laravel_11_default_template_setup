<?php

namespace App\Http\Resources\DeliveryCharge;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryChargeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data = [
            'id' => $this->id,


        ];

        if (!is_null($this->title)) {
            $data['title'] = $this->title;
        }

        if (!is_null($this->cost)) {
            $data['cost'] = $this->cost;
        }

        if (!is_null($this->status)) {
            $data['status'] = $this->status;
        }

        return $data;
    }
}
