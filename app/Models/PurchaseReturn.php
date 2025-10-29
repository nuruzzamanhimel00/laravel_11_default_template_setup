<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseReturn extends Model
{
    use HasFactory, ModelBootHandler;
    protected $guarded = ['id'];

    public function purchase(){
        return $this->belongsTo(Purchase::class,'purchase_id','id');
    }

    public function purchase_return_items(){
        return $this->hasMany(PurchaseReturnItem::class,'purchase_return_id','id');
    }
}
