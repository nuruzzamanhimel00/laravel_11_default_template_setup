<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory, ModelBootHandler, ScopeActive;

    protected $guarded =['id'];

    public function customer(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->hasMany(ReviewImage::class,'review_id','id');
    }

}
