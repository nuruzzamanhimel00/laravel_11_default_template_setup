<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseReceive extends Model
{
    use HasFactory, ModelBootHandler;
    protected $guarded = ['id'];

    public function purchase(){
        return $this->belongsTo(Purchase::class,'purchase_id','id');
    }

    public function purchase_receive_items(){
        return $this->hasMany(PurchaseReceiveItem::class,'purchase_receive_id','id');
    }
}
