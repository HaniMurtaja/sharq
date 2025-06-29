<?php

namespace App\Http\Resources\Admin\Dispatcher;

use App\Enum\OrderStatus;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientOrderSummaryResource extends JsonResource
{
    public function toArray($request): array
    {

        return  [
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'profile' => $this->image,
            'pending_orders_count' => $this->orders->where('status', OrderStatus::PENDINE_DRIVER_ACCEPTANCE)->count(),
            'in_progress_orders_count' => $this->orders->whereIn('status', [OrderStatus::ARRIVED_PICK_UP, OrderStatus::PICKED_UP, OrderStatus::ARRIVED_TO_DROPOFF, OrderStatus::DRIVER_ACCEPTED])->count(),
            'failed_count' => $this->orders->where('status', OrderStatus::FAILED)->count(),
            'cancelled_orders_count' => $this->orders->where('status', OrderStatus::CANCELED)->count(),
            'delivered_orders_count' => $this->orders->where('status', OrderStatus::DELIVERED)->count(),
            'avg_waiting_time' => $this->calculateAvgTime('arrived_to_pickup_time', 'picked_up_time'),
            'avg_delivery_time' => $this->calculateAvgTime('driver_accepted_time', 'arrived_to_dropoff_time'),
        ];
    }

    // private function calculateAvgTime($startColumn, $endColumn)
    // {
    //     $durations = $this->orders->filter(function ($order) use ($startColumn, $endColumn) {
    //         return $order->$startColumn && $order->$endColumn;
    //     })->map(function ($order) use ($startColumn, $endColumn) {
    //         return Carbon::parse($order->$startColumn)->diffInSeconds(Carbon::parse($order->$endColumn));
    //     });

    //     if ($durations->isEmpty()) {
    //         return '00:00:00';
    //     }

    //     $avgSeconds = $durations->avg();
    //     return gmdate('H:i:s', $avgSeconds);
    // }


    private function calculateAvgTime($startColumn, $endColumn)
    {
        $durations = $this->orders->filter(function ($order) use ($startColumn, $endColumn) {
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
      

        $hours = floor($avgSeconds / 3600);
        $minutes = floor(($avgSeconds % 3600) / 60);
        $seconds = $avgSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
