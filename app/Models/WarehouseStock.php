<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class WarehouseStock extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }
    public function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id','id');
    }
    // public function variant(){
    //     return $this->belongsTo(ProductVariant::class,'variant_id','id');
    // }



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

    public function promotion_items(){
        return $this->hasMany(PromotionItem::class,'product_id','product_id');
    }
}
