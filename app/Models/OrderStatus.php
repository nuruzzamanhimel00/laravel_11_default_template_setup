<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\ModelBootHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStatus extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $appends = ['created_formate'];

    //---------- Appends ----------//
    public function getCreatedFormateAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y-m-d h:i A');
    }

    // -------------- Relationship -------------
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
