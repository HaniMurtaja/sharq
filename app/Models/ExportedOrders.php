<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportedOrders extends Model
{
   protected $table = 'exported_orders';

    protected $fillable = [
        'order_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'value',
        'service_fees',
        'total',
        'status',
        'payment_type',
        'driver_id',
        'driver_name',
        'client_account',
        'ingr_shop_id',
        'shop_name',
        'branch_id',
        'branch_name',
        'city',
        'cancel_reason',
        'order_created_at',
        'driver_assigned_at',
        'arrived_to_pickup_time',
        'picked_up_time',
        'arrived_to_dropoff_time',
        'delivered_at',
        'pickup_distance',
        'delivery_distance',
        'pickup_duration',
        'delivery_duration',
        'status_id',
        'city_id',
    ];
}
