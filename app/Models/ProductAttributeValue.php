<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttributeValue extends Model
{
    use HasFactory, ModelBootHandler, ScopeActive;

    protected $guarded =['id'];
    protected $appends = ['image_url'];
    public const FILE_STORE_PATH    = 'attributes';

    public function getImageUrlAttribute()
    {
        return getStorageImage($this->image, false);
    }

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }
}
