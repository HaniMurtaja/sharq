<?php

namespace App\Models;

use App\Enum\Currency;
use App\Enum\OrderStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientDetail extends Model
{
    use HasFactory;
    protected $table = 'clients';
    protected $fillable = [
        'user_id',
        'country_id',
        'city_id',
        'currency',
        'default_prepartion_time',
        'min_prepartion_time',
        'partial_pay',
        'note',
        'client_group_id',
        'driver_group_id',
        'auto_dispatch',
        'has_special_business_hours',
        'is_integration',
        'account_number',
        'price_order',
        'integration_id'
    ];






    protected $casts = [
        'currency' => Currency::class,

        'default_prepartion_time' => 'integer',
        'min_prepartion_time' => 'integer',
        'partial_pay' => 'integer',
    ];



    public function country () {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city () {
        return $this->belongsTo(City::class, 'city_id');
    }



    public function client () {
        return $this->belongsTo(Client::class, 'user_id');
    }

    public function clienGroup () {
        return $this->belongsTo(ClientsGroup::class, 'client_group_id');
    }

    public function driverGroup () {
        return $this->belongsTo(Group::class, 'driver_group_id');
    }

    public function integration () {
        return $this->belongsTo(IntegrationCompany::class, 'integration_id');
    }

    public function WebhookData(): HasMany
    {
        return $this->hasMany(WebHook::class, 'integration_company_id','integration_id');
    }

    public function OrderSalesReport(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        $orders = $this->hasMany(Order::class, 'ingr_shop_id','user_id')->where('status',OrderStatus::DELIVERED);
        if (request()->fromtime) {
            $orders->where('created_at', '>=', Carbon::parse(request()->fromtime)->format('Y-m-d H:i:s'));
        }
        if (request()->totime) {
            $orders->where('created_at', '<=', Carbon::parse(request()->totime)->format('Y-m-d H:i:s'));
        }
        return $orders;
    }

}
