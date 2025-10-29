<?php

namespace App\Http\Resources\Notification;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'title' => 'Order '.$this->data['status'] ?? '', // Fallback to empty string if null
            'message' => $this->data['message'] ?? '', // Fallback to empty string if null
            'order_id' => $this->data['order_id'] ?? '', // Fallback to empty string if null
            'notify_type' => $this->data['notify_type'] ?? '', // Fallback to empty string if null
            'read_at' => !empty($this->read_at) ? Carbon::parse($this->read_at)->format('Y-m-d h:i A') : null,
            'created_date' => Carbon::parse($this->created_at)->format('Y-m-d h:i A'),
            'user_id' => $this->data['user_id'] ?? '', // Fallback to empty string if null
        ];
    }
}
