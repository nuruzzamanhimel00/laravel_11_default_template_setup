<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\ModelBootHandler;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Product extends Model
{
    use SoftDeletes, ModelBootHandler, ScopeActive;
    protected $guarded = ['id'];

    protected $appends = ['image_url','barcode_image_url','rating','created_at_human','details_image_url'];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'taxes' => AsArrayObject::class,
        ];
    }
    /**
     * Interact with the taxes.
     */
    // protected function taxes(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn (string $value) => json_decode($value),
    //     );
    // }

    // ============== constants =====================>
    const FILE_STORE_PATH = "products";
    const FILE_BARCODE_PATH = "products/barcode";

    public function getCreatedAtHumanAttribute()
    {
        return str_replace(' ago', '', Carbon::parse($this->created_at)->diffForHumans());
        // return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function getImageUrlAttribute()
    {
        return getStorageImage($this->image);
    }
    public function getBarcodeImageUrlAttribute()
    {
        return asset('storage/'.$this->barcode_image);
    }
    public function getDetailsImageUrlAttribute()
    {
        return getOnlyStorageImage($this->details_image);
    }
    public function getRatingAttribute()
    {
        $reviews = Review::where('product_id', $this->id)->where('status',STATUS_ACTIVE)->get();
        $averageRating = $reviews->avg('rating') ?? 0;
        $totalReviewCount = $reviews->count() ?? 0;
        return [
            'averageRating' => formatNumberSmart($averageRating),
            'totalReviewCount' => $totalReviewCount
        ];

    }
    // ============== constants =====================>
    // ============== scopes =====================>
    // Add a scope to filter for default warehouse
    public function scopeWithDefaultWarehouseStock($query)
    {
        return $query->with(['defaultWarehouseStock' => function($query) {
            $query->whereHas('warehouse', function($q) {
                $q->where('is_default', true);
            });
        }]);
    }

    public function scopeAvailableFor($query)
    {
        $available_for = auth('api')->check() ? auth('api')->user()->type : null;

        return $query->when(!is_null($available_for), function ($query) use ($available_for) {
            $query->where('available_for', $available_for)
            ->orWhere('available_for', 'Both');
        });
    }

    public function scopeNewArrival($query, $days){
        // dd(Carbon::now()->subDays($days));
        return $query->whereDate('created_at', '>=', Carbon::now()->subDays($days));
    }
    public function scopeStockAvailable($query,$stock_type='in_stock'){
        // return $query->where('total_stock_quantity', '>', 0);
        if(!is_null($stock_type)){
            if( $stock_type == 'in_stock'){
                return $query->where('total_stock_quantity', '>', 0);
            }
            return $query->where('total_stock_quantity', '<=', 0);
        }

    }
    public function scopeHasActiveCategory($query){
        return $query->whereHas('category', function ($query) {
            $query->active();
        });
    }
    public function scopeHasActiveBrand($query){
        return $query->whereHas('brand', function ($query) {
            $query->active();
        });
    }
    // ============== scopes =====================>

    public function review_ratings(){
        $reviews = Review::where('product_id', $this->id)->where('status',STATUS_ACTIVE)->get();
        $averageRating = $reviews->avg('rating') ?? 0;
        $totalReviewCount = $reviews->count() ?? 0;
        return [
            'averageRating' => formatNumberSmart($averageRating),
            'totalReviewCount' => $totalReviewCount
        ];
    }

    // ============== relations =====================>
    // warehouse
    public function warehouses()
    {
        return $this->hasMany(Warehouse::class, 'warehouse_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function productCondition()
    {
        return $this->belongsTo(ProductCondition::class);
    }

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class);
    }

    public function productMeta()
    {
        return $this->hasOne(ProductMeta::class);
    }

    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStock::class);
    }
    public function warehouse_stock()
    {
        return $this->hasOne(WarehouseStock::class);
    }

    public function defaultWarehouseStock()
    {
        return $this->hasOne(WarehouseStock::class)
            ->join('warehouses', 'warehouse_stocks.warehouse_id', '=', 'warehouses.id')
            ->where('warehouses.is_default', true);
    }
    public function order_items(){
        return $this->hasMany(OrderItem::class,'product_id','id');
    }
    public function order_item(){
        return $this->hasOne(OrderItem::class,'product_id','id');
    }
    public function review(){
        return $this->hasOne(Review::class,'product_id','id');
    }
    public function reviews(){
        return $this->hasMany(Review::class,'product_id','id');
    }
    public function promotion_items(){
        return $this->hasMany(PromotionItem::class,'product_id','id');
    }
    public function latest_promotion_item(){
        return $this->hasOne(PromotionItem::class,'product_id','id')->latestOfMany();
    }
    public function product_tags(){
        return $this->hasMany(ProductTag::class,'product_id','id');
    }
    // ============== relations =====================>
}
