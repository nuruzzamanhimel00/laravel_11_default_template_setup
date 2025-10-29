<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\ModelBootHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderPayment extends Model
{
    use HasFactory, ModelBootHandler;
    protected $guarded = ['id'];

    protected $appends = ['date_formate'];

    //---------- Appends ----------//
    public function getDateFormateAttribute()
    {
        $carbon = Carbon::parse($this->date);

        // Format: April 8, 2025 5:56 AM
        return $carbon->format('F j, Y g:i A');
    }

    protected $casts = [
        'account_info' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
