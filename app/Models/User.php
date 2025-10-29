<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Traits\ModelBootHandler;
use App\Traits\Scopes\ScopeActive;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
//use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, ModelBootHandler, ScopeActive, SoftDeletes, HasApiTokens;

    const ADMIN                     = 'admin';
    public const STATUS_ACTIVE      = 'active';
    public const STATUS_INACTIVE    = 'inactive';
    public const FILE_STORE_PATH    = 'users';


    public const TYPE_RESTAURANT       = 'Restaurant';
    public const TYPE_ADMIN         = 'Admin';
    public const TYPE_REGULAR_USER         = 'Customer';
    public const TYPE_DELIVERY_MAN    = 'Delivery Man';
    public const TYPE_SUPPLIER    = 'Supplier';

    public const TYPES              = [
        self::TYPE_RESTAURANT,
        self::TYPE_ADMIN,
        self::TYPE_REGULAR_USER,
        self::TYPE_DELIVERY_MAN,

    ];

    /**
     * appends
     *
     * @var array
     */
    protected $appends = ['avatar_url', 'full_name','is_email_verified','supplier_name','status_badge'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded =['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    protected function getStatusBadgeAttribute(): string
    {
        $badge = $this->status == STATUS_ACTIVE ? 'bg-success' : 'bg-danger';
        if($this->type == self::TYPE_RESTAURANT){
            $status = $this->status == STATUS_ACTIVE ? 'Approve' : 'Reject';
            return '<span class="badge '.$badge.'">'.Str::upper($status).'</span>';
        }
        return '<span class="badge '.$badge.'">'.Str::upper($this->status).'</span>';
    }

    public function getAvatarUrlAttribute()
    {
        return getStorageImage($this->avatar, true);
    }
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function getIsEmailVerifiedAttribute(): string
    {
        return !is_null($this->email_verified_at) ? true : false;
    }
    public function getSupplierNameAttribute(): string
    {
        return $this->type == self::TYPE_SUPPLIER ? $this->supplier->company : '';
        // return $this->type == self::TYPE_SUPPLIER ? $this->supplier->company.' ('.trim($this->full_name).')' : '';
    }

    public function delivery_man(){
        return $this->hasOne(DeliveryMan::class,'user_id','id');
    }

    public function supplier(){
        return $this->hasOne(Supplier::class);
    }
    public function restaurant(){
        return $this->hasOne(Restaurant::class);
    }

    public function user_verify(){
        return $this->hasOne(UserVerify::class);
    }

    public function user_notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');

    }

    public function orders(){
        return $this->hasMany(Order::class,'order_for_id','id');
    }
}
