<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionItem extends Model
{
    protected $guarded =['id'];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
