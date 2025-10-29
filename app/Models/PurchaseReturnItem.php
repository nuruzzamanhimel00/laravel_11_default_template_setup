<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseReturnItem extends Model
{
    use HasFactory, ModelBootHandler;
    protected $guarded = ['id'];

    public function purchase_return(){
        return $this->belongsTo(PurchaseReturn::class,'purchase_return_id','id');
    }
    public function purchase_item(){
        return $this->belongsTo(PurchaseItem::class,'purchase_item_id','id');
    }
    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }
}
