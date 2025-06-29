<?php

namespace App\Models;

use App\Enum\OrderStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientBranches extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'phone',
        'client_group',
        'driver_group',
        'lat',
        'lng',
        'country',
        'city_id',
        'area_id',
        'street',
        'landmark',
        'building',
        'floor',
        'apartment',
        'description',
        'business_hours',
        'auto_dispatch',
        'status',
        'pickup_id',
        'custom_id',
        'is_active',
    ];

    protected $casts = [
        'map_location' => 'json',
        'business_hours' => 'json'
    ];

    // public function user() {
    //     return $this->hasOne(User::class, 'branch_id');
    // }

    public function client () {
        return $this->belongsTo(Client::class);
    }

    public function area () {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function city () {
        return $this->belongsTo(City::class, 'city_id');
    }
    protected $appends = ['image_url','name_branch','orders_count'];

    public function getImageUrlAttribute()
    {
        $pathdafult =  asset('new/src/assets/images/logo-2.png');
        if (isset($this->client->attributes['image'])) {
            if ($this->client->attributes['image'] != null) {
                $path = 'https://alshrouqdelivery.b-cdn.net/'.$this->client->attributes['image'];
                if (file_exists( $path )) {
                    return $pathdafult;
                } else {
                    return $path;
                }
            }
        }
        return $pathdafult;
    }
    public function getNameBranchAttribute()
    {
        return @$this->client->first_name;
    }
    public function getOrdersCountAttribute()
    {
        $count = Order::where('ingr_branch_id',$this->id)
            ->whereNotIn('status',[OrderStatus::DELIVERED->value,OrderStatus::CANCELED->value,OrderStatus::FAILED->value])
            ->whereDate('created_at', '>=', Carbon::yesterday())
            ->whereDate('created_at', '<=', Carbon::today())
            ->count();
        return $count;
    }
    public function getOrders (): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class,'ingr_branch_id')
            ->whereNotIn('status',[OrderStatus::DELIVERED->value,OrderStatus::CANCELED->value,OrderStatus::FAILED->value])
            ->whereDate('created_at', '>=', Carbon::yesterday())
            ->whereDate('created_at', '<=', Carbon::today());
    }
}
