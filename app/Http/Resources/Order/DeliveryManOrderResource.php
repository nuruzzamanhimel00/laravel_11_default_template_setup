<?php

namespace App\Http\Resources\Order;

use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Order\OrderItemResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Order\OrderHistoriesResource;

class DeliveryManOrderResource extends JsonResource
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

        // Basic optional fields
        $optionalFields = [
            // 'delivery_man_id',
            'invoice_no',
            'date',
            // 'order_for_id',
            // 'order_for',
            // 'billing_info',
            // 'shipping_info',
            // 'tax_amount',
            // 'discount_amount',
            // 'global_discount',
            // 'global_discount_type',
            // 'sub_total',
            // 'cancel_date',
            // 'cancel_note',
            // 'delivery_status',
            // 'payment_status',
        ];

        foreach ($optionalFields as $field) {
            if (!is_null($this->$field)) {
                $data[$field] = $this->$field;
            }
        }

        // Special mapped fields
        if (!is_null($this->date_for_human)) {
            $data['time'] = $this->date_for_human;
        }

        // if (!is_null($this->total)) {
        //     $data['price'] = $this->total;
        // }

        // Relationships
        if ($this->relationLoaded('order_items')) {
            // $data['order_items'] = OrderItemResource::collection($this->order_items);
            $data['order_items'] = $this->order_items->count();
        }

        if ($this->relationLoaded('customer') && $this->customer) {
            $data['customer'] = new UserResource($this->customer);
        }
        if ($this->relationLoaded('order_statuses')) {
            $order_histories = [];

            foreach (Order::ORDER_HISTORIES as $order_status) {
                $hasStatus = $this->order_statuses->contains('status', $order_status);

                $order_histories[] = [
                    'status_title' => ucfirst($order_status),
                    'status'       => $hasStatus,
                ];
            }

            $data['order_histories'] = $order_histories;
        }


        // Latest status
        $latestOrderStatus = OrderStatus::where('order_id', $this->id)->latest('id')->first();
        $data['status'] = ucwords($latestOrderStatus->status )?? null;

        return $data;
    }

}
