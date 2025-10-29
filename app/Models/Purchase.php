<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory, ModelBootHandler;
    protected $guarded = ['id'];




    public const FILE_STORE_PATH = 'purchase';


    public const STATUS_REQUESTED     = 'requested';
    public const STATUS_CONFIRMED    = 'confirmed';
    public const STATUS_RECEIVED    = 'received';
    public const STATUS_CANCEL    = 'cancel';


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            if (empty($purchase->purchase_number)) {
                $lastPurchase = Purchase::latest('id')->first();
                $newId = $lastPurchase ? $lastPurchase->id + 1 : 1;
                $purchase->purchase_number = 'PUR-' . date('Ymd') . '-' . str_pad($newId, 6, '0', STR_PAD_LEFT);
                $purchase->status = self::STATUS_REQUESTED;

            }
        });
    }

    public function getUploadFileUrlAttribute()
    {
        return getStorageImage($this->upload_file, true);
    }

    public function supplier(){
        return $this->belongsTo(User::class,'supplier_id','id');
    }


    public function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id','id');
    }
    public function purchase_items(){
        return $this->hasMany(PurchaseItem::class,'purchase_id','id');
    }



    public function purchase_receives(){
        return $this->hasMany(PurchaseReceive::class,'purchase_id','id');
    }
    public function purchase_receive(){
        return $this->hasOne(PurchaseReceive::class,'purchase_id','id');
    }


    public function purchase_returns(){
        return $this->hasMany(PurchaseReturn::class,'purchase_id','id');
    }
    public function purchase_return(){
        return $this->hasOne(PurchaseReturn::class,'purchase_id','id');
    }

}
