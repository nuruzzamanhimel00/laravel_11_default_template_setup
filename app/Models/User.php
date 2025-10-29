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


    public const TYPE_AGENT       = 'Agent';
    public const TYPE_ADMIN         = 'Admin';

    public const TYPES              = [
        self::TYPE_AGENT,
        self::TYPE_ADMIN,


    ];

    /**
     * appends
     *
     * @var array
     */
    protected $appends = ['avatar_url', 'full_name','is_email_verified','status_badge'];

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
        if($this->type == self::TYPE_AGENT){
            $status = $this->status == STATUS_ACTIVE ? 'Active' : 'Inactive';
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



    public function user_verify(){
        return $this->hasOne(UserVerify::class);
    }


}
