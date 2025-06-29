<?php

namespace App\Models;

use App\Enum\OrderStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Enum\JopType;
use App\Enum\VerificationStatuses;
use App\Traits\OrderCreationDateValidation;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatorDetail extends Model
{
    use HasFactory;
    use OrderCreationDateValidation;
    protected $table = 'operators';
    protected $fillable = [
        'operator_id',
        'birth_date',
        'emergency_contact_name',
        'emergency_contact_phone',
        'social_id_no',
        'city_id',
        'iban',
        'status',
        'group_id',
        'branch_group_id',
        'shift_id',
        'days_off',
        'jop_type',
        'lat',
        'lng',
        'order_value',
        'location',
        'app_version',
        'id_card_image_front',
        'id_card_image_back',
        'is_verified',
        'license_front_image',
        'license_back_image'
    ];

    protected $catas = [
        'days_off' => 'json',
        'jop_type' => JopType::class,
        'is_verified' => VerificationStatuses::class,
    ];

    public function getIDCardImageBackAttribute()
    {

        $image = @$this->attributes['id_card_image_back'];


        return $image ? env('IMAGE_URL') . $image : null;
    }
    public function getIDCardImageFrontAttribute()
    {

        $image = @$this->attributes['id_card_image_front'];


        return $image ? env('IMAGE_URL') . $image : null;
    }


 

     public function getLicenseFrontImageAttribute()
    {

        $image = @$this->attributes['license_front_image'];


        return $image ? env('IMAGE_URL') . $image : null;
    }
    public function getLicenseBackImageAttribute()
    {

        $image = @$this->attributes['license_back_image'];


        return $image ? env('IMAGE_URL') . $image : null;
    }    





    public function operator()
    {
        return $this->belongsTo(Operator::class, 'operator_id');
    }
    public function OrdersDate()
    {
        return $this->hasMany(Order::class, 'driver_id', 'operator_id');
    }

    public function OrdersDateWithStatus()
    {
        return $this->hasMany(Order::class, 'driver_id', 'operator_id')->whereIn(
            'status',
            [OrderStatus::ARRIVED_TO_DROPOFF, OrderStatus::ARRIVED_PICK_UP, OrderStatus::PICKED_UP]
        );
    }

    public function OrdersDateWithStatusSameBranch()
    {
        return $this->hasMany(Order::class, 'driver_id', 'operator_id')
            ->whereIn('status', [OrderStatus::DRIVER_ACCEPTED, OrderStatus::ARRIVED_PICK_UP])
            ->where('created_at', '>=', Carbon::now('Asia/Riyadh')->subMinutes(15));
    }
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function branchGroup()
    {
        return $this->belongsTo(Group::class, 'branch_group_id');
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function bank_details(): HasMany
    {
        return $this->hasMany(BankDetails::class, 'operator_id');
    }

    public function DriverOrders(): HasMany
    {
        return $this->hasMany(OrderDriver::class, 'driver_id', 'operator_id')->where(function ($q) {
            $q->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        });
    }

    public function TotalOrderWithDriver()
    {
        return $this->hasMany(Order::class, 'driver_id', 'operator_id')
            ->where('status', OrderStatus::PENDINE_DRIVER_ACCEPTANCE);
    }

    // public function TodayOrdersDateOLD(): HasMany
    // {

    //     return $this->hasMany(Order::class, 'driver_id', 'operator_id')
    //         ->where(function ($q) {
    //             $q->whereDate('created_at', Carbon::today())
    //                 ->orWhereDate('created_at', Carbon::yesterday());
    //         })->whereNotIn('orders.status', [9, 10]);
    // }
    // public function TodayOrdersDate(): HasMany
    // {
    //     return $this->hasMany(\App\Models\Order::class, 'driver_id', 'operator_id')
    //         ->whereBetween('created_at', [Order::getBusinessHours()['startDateTime'],Order::getBusinessHours()['endDateTime']])
    //         ->whereNotIn('orders.status', [9, 10]);
    // }



    public function TodayOrdersDateOld(): HasMany
    {

        return $this->hasMany(Order::class, 'driver_id', 'operator_id')
            ->where(function ($q) {
                $q->whereDate('created_at', Carbon::today())
                    ->orWhereDate('created_at', Carbon::yesterday());
            })->whereNotIn('orders.status', [9, 10]);
    }
    public function TodayOrdersDate(): HasMany
    {
        return $this->hasMany(Order::class, 'driver_id', 'operator_id')
            ->where(function ($q) {
                $q->whereDate('created_at', Carbon::today())
                    ->orWhereDate('created_at', Carbon::yesterday());
            })->whereNotIn('orders.status', [9, 10]);
    }


    public function completed_jobsOld(): int
    {

        return  $this->hasMany(Order::class, 'driver_id', 'operator_id')
            ->where(function ($q) {
                $q->whereDate('created_at', Carbon::today())
                    ->orWhereDate('created_at', Carbon::yesterday());
            })->where('status', 9)->count();
    }
    public function completed_jobs(): int
    {
        $getDateTime = $this->getBusinessHoursIfNowWithinRange();
        return  $this->hasMany(Order::class, 'driver_id', 'operator_id')
               ->whereBetween('created_at', [
                        $getDateTime['start'],
                        $getDateTime['end']
                    ])
            ->where('status', 9)->count();
    }
    public function tasks(): int
    {
        return $this->TodayOrdersDate()->whereNotIn('status', [9, 10])->count() * 2;
    }

    public function OrdersDateWithStatusAndWithoutDELIVERED()
    {
        return $this->hasMany(Order::class, 'driver_id', 'operator_id')->whereNotIn(
            'status',
            [OrderStatus::DELIVERED, OrderStatus::CANCELED]
        )
            ->where('created_at', '>=', Carbon::yesterday())
            ->where('created_at', '<=', Carbon::today());
    }
}
