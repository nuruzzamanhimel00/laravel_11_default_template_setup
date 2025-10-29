<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Restaurant extends Model
{
    use HasFactory, ModelBootHandler, ScopeActive;
    protected $guarded =['id'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
