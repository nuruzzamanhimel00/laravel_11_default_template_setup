<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory, ModelBootHandler, ScopeActive;
    public const FILE_STORE_PATH    = 'brands';

    protected $appends = ['image_url'];

    protected $guarded =['id'];

    public function getImageUrlAttribute()
    {
        return getStorageImage($this->image);
    }
    public function products(){
        return $this->hasMany(Product::class, 'category_id','id');
    }
}
