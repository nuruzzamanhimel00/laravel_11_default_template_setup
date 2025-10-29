<?php

namespace App\Traits;

use Illuminate\Support\Str;
use App\Models\Order;

trait OrderStatusHtmlAttributes
{
    public function getPaymentStatusHtmlAttribute(){
        $badge = $this->payment_status == Order::STATUS_PARTIALLY_PAID ? "bg-info" : "bg-success" ;
        return '<span class="badge ' . $badge . '">' . Str::upper( str_replace('_', ' ', $this->payment_status)) . '</span>';
    }
    public function getOrderStatusHtmlAttribute(){

        $badge = $this->order_status == Order::STATUS_PENDING ? "bg-info" : ( $this->order_status == Order::STATUS_ORDER_PLACED ? "bg-warning ":
        ($this->order_status == Order::STATUS_ORDER_PACKAGING ? "bg-dark " : ($this->order_status == Order::STATUS_ORDER_PACKAGED ? "bg-success " : "bg-danger") )
        ) ;

        return '<span class="badge ' . $badge . '">' . Str::upper( str_replace('_', ' ', $this->order_status)) . '</span>';
    }
    public function getDeliveryStatusHtmlAttribute(){


        $badge = $this->delivery_status == Order::STATUS_DELIVERY_ACCEPTED ? "bg-info" : ( $this->delivery_status == Order::STATUS_DELIVERY_COLLECTED ? "bg-dark":
        ($this->delivery_status == Order::STATUS_DELIVERY_DELIVERED ? "bg-warning" :"bg-success")
        ) ;

        return '<span class="badge ' . $badge . '">' . Str::upper( str_replace('_', ' ', $this->delivery_status)) . '</span>';
    }
    public function getPlatformHtmlAttribute(){
        $badge = $this->platform == Order::PLATFORM_ADMIN ? "bg-info" : "bg-success" ;
        return '<span class="badge ' . $badge . '">' . Str::upper( str_replace('_', ' ', $this->platform)) . '</span>';
    }
}
