<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryMan extends Model
{
    use HasFactory, ModelBootHandler;
    public const FILE_STORE_PATH    = 'nid';

    protected $guarded =['id'];

    protected $appends = ['nid_front_url','nid_back_url'];

    public function getNidFrontUrlAttribute()
    {
        return getStorageImage($this->nid_front, false);
    }
    public function getNidBackUrlAttribute()
    {
        return getStorageImage($this->nid_back, false);
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
