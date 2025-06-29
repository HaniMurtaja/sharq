<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\ExportedOrders;
use Carbon\Carbon;

class ExportOrdersCommand extends Command
{
    protected $signature = 'orders:export';
    protected $description = 'Export orders into exported_orders table';

    public function handle()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        set_time_limit(0);

        Order::where('created_at', '>=', Carbon::parse('2025-05-20'))->whereNull('exported_order_id')->with([
            'DriverDataSearch',
            'ClientDetail',
            'shop',
            'branch',
            'cityData',
            'cancel_reason'
        ])->chunk(500, function ($orders) {
            foreach ($orders as $row) {
                //dd($row->driver_id);
                $exported = ExportedOrders::updateOrCreate(
                    ['order_id' => $row->id],
                    [
                        'order_number' => $row->OrderNumber,
                        'customer_name' => $row->customer_name ,
                        'customer_phone' => $row->customer_phone ,
                        'value' => (string)($row->value ?? '0'),
                        'service_fees' => (string)($row->service_fees ?? '0'),
                        'total' => (string)(($row->value ?? 0) + ($row->service_fees ?? 0)),
                        'status' => optional($row->status)->getLabel() ,
                        'payment_type' => optional($row->payment_type)->getLabel() ,
                        'driver_id' => $row->driver_id ,
                        'driver_name' => optional($row->DriverDataSearch)->full_name ,
                        'client_account' => @$row->ClientDetail->account_number ,
                        'ingr_shop_id' => @$row->ingr_shop_id ,
                        'shop_name' => @$row->shop->full_name ,
                        'ingr_branch_id' => @$row->ingr_branch_id,
                        'branch_name' => @$row->branch->name ,
                        'city' => @$row->cityData->name ,
                        'cancel_reason' => @$row->cancel_reason->name ,
                        'order_created_at' => $row->created_at,
                        'driver_assigned_at' => $row->driver_assigned_at,
                        'arrived_to_pickup_time' => $row->arrived_to_pickup_time,
                        'picked_up_time' => $row->picked_up_time,
                        'arrived_to_dropoff_time' => $row->arrived_to_dropoff_time,
                        'delivered_at' => $row->delivered_at,
                        'pickup_distance' => $row->pickup_distance,
                        'delivery_distance' => $row->delivery_distance,
                        'pickup_duration' => $row->pickup_duration,
                        'delivery_duration' => $row->delivery_duration,
                        'status_id' => $row->status,
                        'city_id' => @$row->city ,
                    ]
                );
                $row->exported_order_id = $exported->id;
                $row->save();
            }
        });

        $this->info('✅ Exported orders successfully');
    }
}
