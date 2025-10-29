<?php

namespace App\Models;

use App\Traits\ModelBootHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewImage extends Model
{
    use HasFactory;
    public const FILE_STORE_PATH    = 'review_images';

    protected $appends = ['image_url'];

    protected $guarded =['id'];

    public function getImageUrlAttribute()
    {
        return getStorageImage($this->image);
    }
}
