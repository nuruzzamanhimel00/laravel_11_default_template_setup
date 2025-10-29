<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttribute extends Model
{
    use HasFactory, ModelBootHandler, ScopeActive;

    protected $guarded =['id'];

    public function values(){
        return $this->hasMany(ProductAttributeValue::class,'product_attribute_id','id');
    }
}
