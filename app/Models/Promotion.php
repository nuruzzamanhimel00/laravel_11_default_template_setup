<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Traits\ModelBootHandler;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory, ModelBootHandler, ScopeActive;

    protected $guarded =['id'];
    protected $appends = ['image_url','status_badge','valid_from','valid_to','promotion_type'];

    // protected $casts = [
    //     'start_date' => 'date',
    //     'end_date' => 'date',
    // ];


    public const FILE_STORE_PATH    = 'promotions';

    const APPLICABLE_PRODUCTS = 'products';
    const APPLICABLE_CATEGORIES = 'categories';

    const APPLICABLE_TYPES = [
        self::APPLICABLE_CATEGORIES,
        self::APPLICABLE_PRODUCTS,
    ];

    const OFFER_TYPE_FIXED = 'fixed';
    const OFFER_TYPE_PERCENTAGE = 'percent';

    const OFFER_TYPES = [
        self::OFFER_TYPE_FIXED,
        self::OFFER_TYPE_PERCENTAGE
    ];


    //------------- appends --------------
    public function getValidFromAttribute()
    {
        return Carbon::parse($this->start_date)->format('F j, Y');
    }
    public function getValidToAttribute()
    {
        return Carbon::parse($this->end_date)->format('F j, Y');
    }

    public function getImageUrlAttribute()
    {
        return getStorageImage($this->image, false);
    }


    protected function getStatusBadgeAttribute(): string
    {
        $badge = $this->status == STATUS_ACTIVE ? 'bg-success' : 'bg-danger';
        return '<span class="badge '.$badge.'">'.Str::upper($this->status).'</span>';
    }

    // public function getPromotionTypeAttribute()
    // {
    //     $now = now();

    //     if ($now->between($this->start_date, $this->end_date)) {
    //         return 'Running';
    //     } elseif ($now->lt($this->start_date)) {
    //         return 'Upcoming';
    //     } else {
    //         return 'Expired';
    //     }
    // }

    public function getPromotionTypeAttribute()
    {
        $today = now()->startOfDay(); // Normalize to date only

        $startDate = Carbon::parse($this->start_date)->startOfDay();
        $endDate = Carbon::parse($this->end_date)->endOfDay(); // Include full end date

        if ($today->lt($startDate)) {
            return 'Upcoming';
        } elseif ($today->gt($endDate)) {
            return 'Expired';
        } else {
            return 'Running';
        }
}

    //------------- appends end --------------
    //------------- function --------------
    //------------- function end --------------
    //------------- relationships --------------

    public function promotion_items(){
        return $this->hasMany(PromotionItem::class,'promotion_id','id');
    }
    //------------- relationships end --------------

}
