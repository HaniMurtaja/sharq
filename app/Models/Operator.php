<?php

namespace App\Models;

use App\Models\BankDetails;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Operator extends User  implements HasMedia
{
    use HasFactory;
    use HasRoles, InteractsWithMedia;
    protected string $guard_name = 'web';
    const  ROLE = 'operator';
    protected $table = 'users';

    public function getMorphClass(): string {
        return User::class;
    }

    protected static function booted() {
        parent::booted();
        static::creating(fn($model) => $model->assignRole('operator'));
        static::addGlobalScope("operator", function ($builder) {
            $builder->whereHas("roles", fn($q) => $q->where('name', 'operator'));
        });
    }

    public function operator () {

        return $this->hasOne(OperatorDetail::class, 'operator_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_drivers', 'driver_id', 'order_id');
    }


    public function OrderData()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }
    public function driverOrders () {
        return $this->hasMany(OrderDriver::class, 'driver_id');
    }
    public function wallet(){
        return $this->hasOne(Wallet::class, 'operator_id');
    }
    public function DriverBranchAndClient()
    {
        return $this->hasMany(Order::class, 'driver_id')
           ->whereIn('status',[2,6,8,16,17]);
    }
    public function DriverBranchAndClientUpdated()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        return $this->hasMany(Order::class, 'driver_id')
           ->whereIn('status',[2,6,8,16,17])->whereDate('created_at',">=",$yesterday)
           ->WhereDate('created_at', "<=",$today);;
    }


    public function statuses() {

        return $this->hasMany(OperatorStatus::class, 'operator_id');
    }
    public function getOrders()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }
    public function CurrentOrdersWithDriver()
    {
        return $this->hasMany(Order::class, 'driver_id','id')
            -> where(function ($q){
                $q->whereDate('created_at', Carbon::yesterday())
                    ->orWhereDate('created_at', Carbon::today());
            });


    }

  


    public function calculateAvgTime($startColumn, $endColumn)
    {
        $durations = $this->OrderData->filter(function ($order) use ($startColumn, $endColumn) {
            return $order->$startColumn && $order->$endColumn;
        })->map(function ($order) use ($startColumn, $endColumn) {
            $startDate = Carbon::parse($order->$startColumn);
            $endDate = Carbon::parse($order->$endColumn);


            $diffInSeconds = $startDate->diffInSeconds($endDate);
           
            return $diffInSeconds;
        });

        if ($durations->isEmpty()) {
            return '00:00:00';
        }

        $avgSeconds = $durations->avg();
      

        // Handle durations longer than 24 hours
        $hours = floor($avgSeconds / 3600);
        $minutes = floor(($avgSeconds % 3600) / 60);
        $seconds = $avgSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
