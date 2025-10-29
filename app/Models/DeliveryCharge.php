<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryCharge extends Model
{

    use HasFactory, ModelBootHandler, ScopeActive;

    protected $guarded =['id'];

    protected $casts = [
        'cost' => 'float',
    ];
}
