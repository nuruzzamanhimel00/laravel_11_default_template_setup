<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Traits\ModelBootHandler;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Database\Eloquent\Model;
use App\Traits\OrderStatusHtmlAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, ModelBootHandler, ScopeActive, OrderStatusHtmlAttributes;
    protected $guarded = ['id'];

    protected $casts = [
        'billing_info' => 'array',
        'shipping_info' => 'array',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'global_discount' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'total' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'order_for_id' => 'integer',
    ];


    protected $appends = ['payment_status_html','order_status_html','platform_html','delivery_status_html'];

    //---------- Const ----------//
    public const STATUS_PENDING  = 'pending';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCEL = 'cancel';
    public const STATUS_ORDER_PLACED = 'placed';
    public const STATUS_ORDER_PACKAGING = 'packaging';
    public const STATUS_ORDER_PACKAGED = 'packaged';

    public const STATUS_DELIVERY_ACCEPTED = 'accepted';
    public const STATUS_DELIVERY_COLLECTED = 'collected';
    public const STATUS_DELIVERY_DELIVERED = 'delivered';
    public const STATUS_DELIVERY_COMPLETE = 'received';

    public const PLATFORM_ADMIN = 'system';
    public const PLATFORM_MOBILE = 'mobile';

    public const ORDER_HISTORIES = [
        (Order::STATUS_PENDING),
        (Order::STATUS_ORDER_PLACED),
        (Order::STATUS_ORDER_PACKAGING),
        (Order::STATUS_ORDER_PACKAGED),
        (Order::STATUS_DELIVERY_ACCEPTED),
        (Order::STATUS_DELIVERY_COLLECTED),
        (Order::STATUS_DELIVERY_DELIVERED),
        (Order::STATUS_DELIVERY_COMPLETE),
    ];



    //---------- Boot ----------//
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($sale) {
    //         if (empty($sale->invoice_no)) {
    //             $lastSale = self::latest('id')->first();
    //             $newId = $lastSale ? $lastSale->id : 1;
    //             $sale->invoice_no = 'INV-'. rand(1000,9999).'-' . str_pad($newId, 4, '0', STR_PAD_LEFT);

    //         }
    //     });
    // }
    //---------- Boot ----------//

    public function getDateForHumanAttribute()
    {
        // Parse the date and set the time from created_at
        $date = Carbon::parse($this->date);
        $createdAt = Carbon::parse($this->created_at);
        $date->setTime($createdAt->hour, $createdAt->minute, $createdAt->second);

        // Now calculate the difference using diffForHumans
        $diff = $date->diffForHumans(null, true); // true = short format

        return $diff . ' ago';
    }
    //---------- Relations ----------//
    public function order_items()
    {
        return $this->hasMany(OrderItem::class,'order_id','id');
    }

    public function order_payments()
    {
        return $this->hasMany(OrderPayment::class,'order_id','id');
    }
    public function order_payment()
    {
        return $this->hasOne(OrderPayment::class,'order_id','id');
    }
    public function order_status()
    {
        return $this->hasOne(OrderStatus::class,'order_id','id');
    }
    public function order_statuses()
    {
        return $this->hasMany(OrderStatus::class,'order_id','id');
    }

    public function customer(){
        return $this->belongsTo(User::class,'order_for_id','id');
    }
    public function warehouse_stock(){
        return $this->belongsTo(WarehouseStock::class,'warehouse_stock_id','id');
    }
    public function delivery_man(){
        return $this->belongsTo(User::class,'delivery_man_id','id');
    }

    //---------- Relations ----------//

    // ---------- Scopes ----------//
    public function scopeDelivered($query)
    {
        return $query->where('order_status', self::STATUS_ORDER_PACKAGED)
            ->where('delivery_status', self::STATUS_DELIVERY_COMPLETE);
    }

    // last month
    public function scopeLastMonth($query)
    {
        // $lastMonthStartDate = now()->subMonth(1)->startOfMonth();
        // $lastMonthEndDate = now()->subMonth(1)->endOfMonth();
        return $query->whereMonth('date', now()->subMonth(1));
    }
    // ---------- Scopes ----------//

}
