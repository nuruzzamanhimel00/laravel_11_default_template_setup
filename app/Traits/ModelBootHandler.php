<?php

namespace App\Traits;

use App\Models\Product;

trait ModelBootHandler
{
    public static function booted()
    {
        parent::boot();

        static::creating(function($model){
            $model->created_by = auth()->id() ?? null;

            // if model == Product
            // if(get_class($model) == Product::class) {
            //     $model['taxes'] = json_encode($model->taxes);
            // }
        });

        static::updating(function($model){
            $model->updated_by = auth()->id() ?? null;

             // if model == Product
            //  if(get_class($model) == Product::class) {
            //     $model['taxes'] = json_encode($model->taxes);
            // }
        });

        static::deleting(function($model){
            // if model has image field and is not empty
            $fileFields = ['image', 'avatar', 'banner', 'file', 'video', 'thumbnail', 'barcode_image'];
            foreach ($fileFields as $field) {
                if ($model->{$field}) {
                    deleteFileFromStorage($model->{$field});
                }
            }

            if(get_class($model) == Product::class){
                if($model?->barcode_image){
                    deleteFileFromStorage($model?->barcode_image);
                }
            }
        });
    }
}
