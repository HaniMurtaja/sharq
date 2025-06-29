<?php

namespace App\Models;

use App\Enum\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;


use Spatie\MediaLibrary\HasMedia;



class User extends Authenticatable implements HasMedia
{
    use HasRoles;
    use HasApiTokens, HasFactory, InteractsWithMedia;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'firebase_token',
        'device_id',
        'integration_token',
        'user_role',
        'image',
        'client_id',
        'branch_id',
        'is_active',
        'deleted_by',
        'country_id'

    ];

    protected $appends = ['full_name', 'image', 'image_url'];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'user_role' => UserRole::class
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }


    public function country() {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function getImageAttribute()
    {

        $image = @$this->attributes['image'];


        return $image ? env('IMAGE_URL') . $image : null;
    }
    public function getImageUrlAttribute()
    {

        $image = @$this->attributes['image'];


        // return asset('maps/Capture2.PNG');
        return $image ? env('IMAGE_URL') . $image : null;
    }

    public function ScopeOperatorOnly(Builder $query): Builder
    {
        return $query->where('user_role', UserRole::OPERATOR);
    }

    public function groups()
    {
        return $this->hasMany(UserGroups::class, 'user_id');
    }
    public function getUserCitys()
    {
        return $this->hasMany(UserCitys::class, 'user_id');
    }

    public function cities()
    {
        return $this->hasMany(OperatorCity::class, 'operator_id');
    }

    public function operator()
    {
        return $this->hasOne(OperatorDetail::class, 'operator_id', 'id');
    }


    public function client()
    {

        return $this->hasOne(ClientDetail::class, 'user_id');
    }
    public function notifications()
    {
        return $this->hasMany(UserNotification::class, 'user_id');
    }

    public function unReaNotificationsCustom()
    {
        return $this->notifications()->where('is_read', 0);
    }


    public function branch()
    {
        return $this->belongsTo(ClientBranches::class, 'branch_id');
    }
    public function getOperatorDetail()
    {
        return $this->hasOne(OperatorDetail::class, 'operator_id');
    }

    public function OperatorOrders()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        return $this->hasMany(Order::class, 'driver_id')
            ->whereNotNull('driver_accepted_time')->whereDate('created_at', ">=", $yesterday)
            ->WhereDate('created_at', "<=", $today);
    }
    public function ClientOrders()
    {
        return $this->hasMany(Order::class, 'ingr_shop_id');
    }
}
