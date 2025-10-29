<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class OrderItem extends Model
{
    use HasFactory, ModelBootHandler, ScopeActive;
    protected $guarded = ['id'];

    protected $casts = [
        'data' => 'array',
        'price' => 'decimal:2',
        'sub_total' => 'decimal:2',
    ];
    //------------ Relationships ------------
    public function order()
    {
        return $this->belongsTo(Order::class,'order_id','id');
    }
    // public function product_variant(){
    //     return $this->belongsTo(ProductVariant::class,'variant_id','id');
    // }
    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function category(): HasOneThrough
    {
        return $this->hasOneThrough(
            Category::class,
            Product::class,
            'id', // Foreign key on the products table...
            'id', // Foreign key on the product_conditions table...
            'product_id', // Local key on the products table...
            'category_id' // Local key on the cars table...
        );
    }

    public function brand(): HasOneThrough
    {
        return $this->hasOneThrough(
            Brand::class,
            Product::class,
            'id', // Foreign key on the products table...
            'id', // Foreign key on the product_conditions table...
            'product_id', // Local key on the products table...
            'brand_id' // Local key on the cars table...
        );
    }



    // public function product_variant_condition(): HasOneThrough
    // {
    //     return $this->hasOneThrough(
    //         ProductCondition::class,
    //         ProductVariant::class,
    //         'id', // Foreign key on the products table...
    //         'id', // Foreign key on the product_conditions table...
    //         'variant_id', // Local key on the products table...
    //         'product_condition_id' // Local key on the cars table...
    //     );
    // }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,'warehouse_id','id');
    }
    public function warehouse_stock()
    {
        return $this->belongsTo(WarehouseStock::class,'warehouse_stock_id','id');
    }


    public function sale_payment()
    {
        return $this->hasOne(OrderPayment::class,'order_id','order_id');
    }
    //------------ Relationships ------------
}
