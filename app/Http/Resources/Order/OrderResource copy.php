<?php

namespace App\Http\Resources\Order;

use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Order\OrderItemResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Order\OrderHistoriesResource;

class OrderResource extends JsonResource
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
        if (!is_null($this->delivery_man_id)) {
            $data['delivery_man_id'] = $this->delivery_man_id;
        }
        if (!is_null($this->invoice_no)) {
            $data['invoice_no'] = $this->invoice_no;
        }
        if (!is_null($this->date)) {
            $data['date'] = $this->date;
        }
        if (!is_null($this->date_for_human)) {
            $data['time'] = $this->date_for_human;
        }
        if (!is_null($this->order_for_id)) {
            $data['order_for_id'] = $this->order_for_id;
        }
        if (!is_null($this->order_for)) {
            $data['order_for'] = $this->order_for;
        }
        if (!is_null($this->billing_info)) {
            $data['billing_info'] = $this->billing_info;
        }
        if (!is_null($this->shipping_info)) {
            $data['shipping_info'] = $this->shipping_info;
        }
        if (!is_null($this->tax_amount)) {
            $data['tax_amount'] = $this->tax_amount;
        }
        if (!is_null($this->discount_amount)) {
            $data['discount_amount'] = $this->discount_amount;
        }
        if (!is_null($this->global_discount)) {
            $data['global_discount'] = $this->global_discount;
        }
        if (!is_null($this->global_discount_type)) {
            $data['global_discount_type'] = $this->global_discount_type;
        }
        if (!is_null($this->sub_total)) {
            $data['sub_total'] = $this->sub_total;
        }
        if (!is_null($this->total)) {
            $data['price'] = $this->total;
        }
        // if (!is_null($this->order_status)) {
        //     $data['order_status'] = $this->order_status;
        // }
        if (!is_null($this->delivery_status)) {
            $data['delivery_status'] = $this->delivery_status;
        }
        if (!is_null($this->payment_status)) {
            $data['payment_status'] = $this->payment_status;
        }
        if (!is_null($this->cancel_date)) {
            $data['cancel_date'] = $this->cancel_date;
        }
        if (!is_null($this->cancel_note)) {
            $data['cancel_note'] = $this->cancel_note;
        }

        if ($this->relationLoaded('order_items') && $this->order_items) {
            $data['order_items'] =OrderItemResource::collection($this->whenLoaded('order_items'));
        }
        if ($this->relationLoaded('customer') && $this->customer) {
            $data['customer'] = new UserResource($this->whenLoaded('customer'));
        }
        if ($this->relationLoaded('order_statuses') && $this->order_statuses) {
            $data['order_histories'] = OrderHistoriesResource::collection($this->whenLoaded('order_statuses'));
        }
        $latestOrderStatus = OrderStatus::where('order_id', $this->id)->orderBy('id', 'desc')->first();
        $data['status'] = !empty($latestOrderStatus) ? $latestOrderStatus->status : null;

        // dd($data);
        return $data;
        // return [
        //     'id' => $this->id,
        //     'delivery_man_id' => $this->delivery_man_id ?? '',
        //     'invoice_no' => $this->invoice_no ?? '',
        //     'date' => $this->date ?? '',
        //     'date_for_human' => $this->date_for_human ?? '',
        //     'order_for_id' => $this->order_for_id ?? null,
        //     'order_for' => $this->order_for ?? null,
        //     'billing_info' => $this->billing_info ?? null,
        //     'shipping_info' => $this->shipping_info ?? '',
        //     'tax_amount' => $this->tax_amount ?? '',
        //     'discount_amount' => $this->discount_amount ?? '',
        //     'global_discount' => $this->global_discount ?? '',
        //     'global_discount_type' => $this->global_discount_type ?? '',
        //     'sub_total' => $this->sub_total ?? '',
        //     'total' => $this->total ?? '',
        //     'order_status' => $this->order_status ?? '',
        //     'delivery_status' => $this->delivery_status ?? '',
        //     'payment_status' => $this->payment_status ?? '',
        //     'cancel_date' => $this->cancel_date ?? '',
        //     'cancel_note' => $this->cancel_note ?? '',
        //     'order_items' => OrderItemResource::collection($this->whenLoaded('order_items')),
        //     'customer' => new UserResource($this->whenLoaded('customer')),
        //     'order_histories' => OrderHistoriesResource::collection($this->whenLoaded('order_statuses')),


        // ];
    }
}
