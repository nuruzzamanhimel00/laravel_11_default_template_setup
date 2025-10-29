<?php

namespace App\Http\Resources\User;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    // public function toArray(Request $request): array
    // {
    //     return [
    //         'id' => $this->id,
    //         'name' => $this->first_name ?? '', // Fallback to empty string if null
    //         'email' => $this->email ?? '', // Fallback to empty string if null
    //         'phone' => $this->phone ?? null, // Return null if image is not set
    //         'type' => $this->type ?? null, // Return null if image is not set
    //         'avatar' => $this->avatar ?? null, // Return null if image is not set
    //         'avatar_url' => $this->avatar_url ?? null, // Return null if image is not set
    //         'full_name' => $this->full_name ?? null, // Return null if image is not set
    //         'restaurant_info' => $this->whenLoaded('restaurant')
    //     ];
    // }
    public function toArray(Request $request): array
    {


        $data = [
            'id' => $this->id,
            // 'name' => $this->first_name ?? '',
            'email' => $this->email ?? '',
        ];

        if (!is_null($this->phone)) {
            $data['phone'] = $this->phone;
        }

        if (!is_null($this->type)) {
            $data['type'] = $this->type;
        }

        if (!is_null($this->avatar)) {
            // $data['avatar'] = $this->avatar;
            $data['avatar_url'] = $this->avatar_url ?? null;
        }

        if (!is_null($this->full_name)) {
            $data['full_name'] = $this->full_name;
        }

        // if ($this->relationLoaded('restaurant') && $this->restaurant) {
        //     $data['restaurant_info'] =$this->whenLoaded('restaurant');
        // }
        if ($this->type == User::TYPE_RESTAURANT && $this->relationLoaded('restaurant') && $this->restaurant) {
            $restaurant = $this->whenLoaded('restaurant');
            // dd($restaurant);
            if ($restaurant) {
                $data['manager_phone'] = $restaurant->manager_phone;
                $data['restaurant_address'] = $restaurant->address;
            }
        }

        // if ($this->type == User::TYPE_RESTAURANT) {
            $userId = $this->id;

            $totalOrderAmount = Order::where("order_for_id", $userId)
                ->where('order_status', '!=', Order::STATUS_CANCEL)
                ->sum('total');

            $totalPaidAmount = OrderPayment::whereHas('order', function ($query) use ($userId) {
                $query->where('order_for_id', $userId)
                    ->where('order_status', '!=', Order::STATUS_CANCEL);
            })->sum('amount');

            $totalDueAmount = $totalOrderAmount - $totalPaidAmount;

            // Use number_format for display (string)
            $data['total_order_amount'] = addCurrency($totalOrderAmount) ?? 0;
            $data['total_paid_amount']  = addCurrency($totalPaidAmount) ?? 0;
            $data['total_due_amount']   = addCurrency($totalDueAmount) ?? 0;

        // }

        return $data;
    }

}
