<?php

namespace App\Http\Resources\Admin\Dispatcher;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;


class OperatorOrderSummaryResource extends JsonResource
{
    public function toArray($request)
    {
        // dd($this->orders);

        return [

            'full_name' => $this->first_name . ' ' . $this->last_name,
            'orders_count' => $this->orders->count(),

            'avg_accept_time' => $this->calculateAvgTime('driver_assigned_at', 'driver_accepted_time'),
        ];
    }

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
