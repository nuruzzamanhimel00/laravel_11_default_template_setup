<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class PurchaseItem extends Model
{
    use HasFactory,ModelBootHandler;
    protected $guarded = ['id'];

    protected $casts = [
        'data' => 'array'
    ];


    public function purchase(){
        return $this->belongsTo(Purchase::class,'purchase_id','id');
    }
    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }
    // public function product_variant(){
    //     return $this->belongsTo(ProductVariant::class,'product_variant_id','id');
    // }

    public function purchase_receive_items(){
        return $this->hasMany(PurchaseReceiveItem::class,'purchase_item_id','id');
    }
    public function purchase_return_items(){
        return $this->hasMany(PurchaseReturnItem::class,'purchase_item_id','id');
    }

    // public function product_condition(): HasOneThrough
    // {
    //     return $this->hasOneThrough(
    //         ProductCondition::class,
    //         Product::class,
    //         'id', // Foreign key on the products table...
    //         'id', // Foreign key on the product_conditions table...
    //         'product_id', // Local key on the products table...
    //         'product_condition_id' // Local key on the cars table...
    //     );
    // }
    // public function product_variant_condition(): HasOneThrough
    // {
    //     return $this->hasOneThrough(
    //         ProductCondition::class,
    //         ProductVariant::class,
    //         'id', // Foreign key on the products table...
    //         'id', // Foreign key on the product_conditions table...
    //         'product_variant_id', // Local key on the products table...
    //         'product_condition_id' // Local key on the cars table...
    //     );
    // }
    public function warehouse(): HasOneThrough
    {
        return $this->hasOneThrough(
            Warehouse::class,
            Purchase::class,
            'id', // Foreign key on the products table...
            'id', // Foreign key on the product_conditions table...
            'purchase_id', // Local key on the products table...
            'warehouse_id' // Local key on the cars table...
        );
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

    public function supplier(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            Purchase::class,
            'id', // Foreign key on the products table...
            'id', // Foreign key on the product_conditions table...
            'purchase_id', // Local key on the products table...
            'supplier_id' // Local key on the cars table...
        );
    }
}
