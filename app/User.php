<?php

namespace App;

use App\Enums\BalanceType;
use App\Models\Balance;
use App\Models\DeliveryBoyAccount;
use App\Models\Order;
use App\Models\Shop;
use App\Models\UserDeposit;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Shipu\Watchable\Traits\HasModelEvents;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, HasMedia
{
    use Notifiable, HasMediaTrait, HasModelEvents, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'username', 'password', 'phone', 'address', 'roles', 'device_token', 'status', 'applied',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['myrole'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function deliveryBoyAccount()
    {
        return $this->hasOne(DeliveryBoyAccount::class, 'user_id', 'id');
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function balance()
    {
        return $this->belongsTo(Balance::class);
    }

    public function getImagesAttribute()
    {
        if (!empty($this->getFirstMediaUrl('user'))) {
            return asset($this->getFirstMediaUrl('user'));
        }
        return asset('assets/img/default/user.png');
    }

    public function OnModelCreating()
    {
        $balance               = new Balance();
        $balance->name         = $this->username;
        $balance->type         = BalanceType::REGULAR;
        $balance->balance      = 0;
        $balance->creator_type = 1;
        $balance->creator_id   = 1;
        $balance->editor_type  = 1;
        $balance->editor_id    = 1;
        $balance->save();

        $this->balance_id = $balance->id;
    }

    public function OnModelCreated()
    {
        $deposit                 = new UserDeposit;
        $deposit->user_id        = $this->id;
        $deposit->deposit_amount = 0;
        $deposit->limit_amount   = 0;
        $deposit->save();
    }

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    /**
     * Route notifications for the FCM channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForFcm($notification)
    {
        return $this->device_token;
    }

    public function getMyroleAttribute()
    {
        return $this->roles->pluck('id', 'id')->first();
    }

    public function getrole()
    {
        return $this->hasOne(Role::class, 'id', 'myrole');
    }

    public function deposit()
    {
        return $this->hasOne(UserDeposit::class);
    }

    public function getMyStatusAttribute()
    {
        return trans('user_statuses.' . $this->status);
    }
}
