<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryStatus extends Model
{
    use HasFactory, ModelBootHandler, ScopeActive;
    protected $guarded = ['id'];

    public function delivery_request()
    {
        return $this->belongsTo(DeliveryRequest::class,'delivery_request_id','id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id','id');
    }
}
