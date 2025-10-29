<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, ModelBootHandler, ScopeActive;
    public const FILE_STORE_PATH    = 'categories';

    protected $appends = ['image_url'];

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded =['id'];


    /**
     * Returns the avatar URL attribute.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        return getStorageImage($this->image, false);
    }

    // ===================== scopes ===============================>
    public function scopeParent($query)
    {
        return $query->where('parent_id', null);
    }
    public function scopeChildCategory($query)
    {
        return $query->where('parent_id', '!=', null);
    }
    // ===================== scopes ==============================

    public function childs(){
        return $this->hasMany(Category::class, 'parent_id')->with(['childs']);
    }

    public function parent(){
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function parents(){
        return $this->belongsTo(Category::class, 'parent_id')->with(['parent']);
    }
    public function products(){
        return $this->hasMany(Product::class, 'category_id','id');
    }
}
