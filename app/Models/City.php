<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'country_id', 'lat', 'lng', 'auto_dispatch'];

    public function country () {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function branches()
    {
        return $this->hasMany(ClientBranches::class, 'city_id');
    }
    public function getOrders()
    {
        return $this->hasMany(Order::class, 'city');
    }

    public function drivers()
    {
        return $this->hasMany(OperatorDetail::class, 'city_id');
    }
    public function getCountOrdersAttribute()
    {
       return 0;
        $query = $this->getOrders();
        if(request()->fromtime != ''){
            $fromtime = date("Y-m-d H:i:s", strtotime(request()->fromtime));
            $query->where("created_at",">=",$fromtime);
        }
        if(request()->totime != ''){
            $totimetime = date("Y-m-d H:i:s", strtotime(request()->totime));
            $query->where("created_at","<=",$totimetime);
        }
        return $query->count();
    }
    public function getCountDriverAttribute()
    {
        $query = User::whereHas('getOperatorDetail', function($q){
            $q->where('city_id',$this->id);
        });
        if(request()->fromtime != ''){
            $fromtime = date("Y-m-d H:i:s", strtotime(request()->fromtime));
          //  $query->where("created_at",">=",$fromtime);
        }
        if(request()->totime != ''){
            $totimetime = date("Y-m-d H:i:s", strtotime(request()->totime));
           // $query->where("created_at","<=",$totimetime);
        }
        return $query->count();
    }
    public function getDriverAssignedAttribute()
    {
        //return 0;
        $query = $this->getOrders()
            ->whereNotNull('driver_assigned_at');

        if (request()->fromtime != '') {
            $fromtime = date("Y-m-d H:i:s", strtotime(request()->fromtime));
            $query->where("created_at", ">=", $fromtime);
        }
        if (request()->totime != '') {
            $totimetime = date("Y-m-d H:i:s", strtotime(request()->totime));
            $query->where("created_at", "<=", $totimetime);
        }
        return $query->selectRaw("TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, created_at, driver_assigned_at))), '%H:%i:%s') AS avg_time")
        ->value('avg_time') ?? '00:00:00';
        // return $query->selectRaw('SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, created_at, driver_assigned_at))) AS total_time')
        // ->value('total_time') ?? '00:00:00';
    }
    public function getDriverAcceptanceAttribute()
    {
       // return 0;
        $query = $this->getOrders()
            ->join('order_logs as ol', function ($join) {
                $join->on('orders.id', '=', 'ol.order_id')
                    ->where('ol.status', '=', 17);
            })
            ->whereNotNull('orders.driver_assigned_at')
            ->whereNotNull('ol.created_at');

        if (request()->fromtime != '') {
            $fromtime = date("Y-m-d H:i:s", strtotime(request()->fromtime));
            $query->where("orders.created_at", ">=", $fromtime);
        }
        if (request()->totime != '') {
            $totimetime = date("Y-m-d H:i:s", strtotime(request()->totime));
            $query->where("orders.created_at", "<=", $totimetime);
        }

        return $query->selectRaw("TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.driver_assigned_at, ol.created_at))), '%H:%i:%s') AS total_acceptance_time")
        ->value('total_acceptance_time') ?? '00:00:00';
        // return $query->selectRaw('SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, orders.driver_assigned_at, ol.created_at))) AS total_acceptance_time')
        // ->value('total_acceptance_time') ?? '00:00:00';
    }
    public function getDriverArrivalAttribute()
    {
        return 0;
        $query = Order::where('city', $this->id)
            ->join('order_logs as ol_start', function ($join) {
                $join->on('orders.id', '=', 'ol_start.order_id')
                    ->where('ol_start.status', '=', 17);
            })
            ->join('order_logs as ol_end', function ($join) {
                $join->on('orders.id', '=', 'ol_end.order_id')
                    ->whereIn('ol_end.status', [16]); // Target statuses
            })
            ->whereNotNull('ol_start.created_at')
            ->whereNotNull('ol_end.created_at');

        if (request()->fromtime != '') {
            $fromtime = date("Y-m-d H:i:s", strtotime(request()->fromtime));
            $query->where("orders.created_at", ">=", $fromtime);
        }
        if (request()->totime != '') {
            $totimetime = date("Y-m-d H:i:s", strtotime(request()->totime));
            $query->where("orders.created_at", "<=", $totimetime);
        }

        return $query->selectRaw('SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, ol_start.created_at, ol_end.created_at))) AS total_status_time')
            ->value('total_status_time') ?? '00:00:00';
    }

    public function getDriverWaitingAttribute()
    {
        return 0;
        $query = Order::where('city', $this->id)
            ->join('order_logs as ol_start', function ($join) {
                $join->on('orders.id', '=', 'ol_start.order_id')
                    ->where('ol_start.status', '=', 16);
            })
            ->join('order_logs as ol_end', function ($join) {
                $join->on('orders.id', '=', 'ol_end.order_id')
                    ->whereIn('ol_end.status', [6]); // Target statuses
            })
            ->whereNotNull('ol_start.created_at')
            ->whereNotNull('ol_end.created_at');

        if (request()->fromtime != '') {
            $fromtime = date("Y-m-d H:i:s", strtotime(request()->fromtime));
            $query->where("orders.created_at", ">=", $fromtime);
        }
        if (request()->totime != '') {
            $totimetime = date("Y-m-d H:i:s", strtotime(request()->totime));
            $query->where("orders.created_at", "<=", $totimetime);
        }

        return $query->selectRaw('SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, ol_start.created_at, ol_end.created_at))) AS total_status_time')
            ->value('total_status_time') ?? '00:00:00';
    }

    public function getDriverDeliverAttribute()
    {
        return 0;
        $query = Order::where('city', $this->id)
        ->join('order_logs as ol_start', function ($join) {
            $join->on('orders.id', '=', 'ol_start.order_id')
                ->where('ol_start.status', '=', 6);
        })
        ->join('order_logs as ol_end', function ($join) {
            $join->on('orders.id', '=', 'ol_end.order_id')
                ->whereIn('ol_end.status', [9]); // Target statuses
        })
        ->whereNotNull('ol_start.created_at')
        ->whereNotNull('ol_end.created_at');

        if (request()->fromtime != '') {
            $fromtime = date("Y-m-d H:i:s", strtotime(request()->fromtime));
            $query->where("orders.created_at", ">=", $fromtime);
        }
        if (request()->totime != '') {
            $totimetime = date("Y-m-d H:i:s", strtotime(request()->totime));
            $query->where("orders.created_at", "<=", $totimetime);
        }

        return $query->selectRaw('SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, ol_start.created_at, ol_end.created_at))) AS total_status_time')
            ->value('total_status_time') ?? '00:00:00';
    }

    public function getSumUTRAttribute()
    {
        if ($this->CountDriver == 0) {
            // Handle the case where there are no drivers
            return 0;
        } else {
            $temp = ($this->CountOrders / $this->CountDriver);
            return round($temp,2);
        }
        return $temp ;
    }


}
