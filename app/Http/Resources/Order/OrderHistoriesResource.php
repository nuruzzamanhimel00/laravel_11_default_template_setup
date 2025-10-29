<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Order\OrderItemResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderHistoriesResource extends JsonResource
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
            'title' => $this->title ?? '',
            'message' => $this->message ?? '',
            'created_date' => $this->created_formate ?? '',
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
