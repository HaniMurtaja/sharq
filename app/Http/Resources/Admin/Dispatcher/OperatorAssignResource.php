<?php

namespace App\Http\Resources\Admin\Dispatcher;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;


class OperatorAssignResource extends JsonResource
{
    public function toArray($request)
    {
        // $avgAcceptTime = $this->OrderData
        //     ->filter(fn($order) => $order->driver_accepted_time && $order->driver_assigned_at)
        //     ->map(fn($order) =>
        //         strtotime($order->driver_accepted_time) - strtotime($order->driver_assigned_at)
        //     )->avg();

        return [
            'id' => $this->id,
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'orders_count' => $this->OrderData->count(),
            'city' => $this->operator?->city?->name,
            'avg_accept_time' => $this->calculateAvgTime('driver_assigned_at', 'driver_accepted_time'),
        ];
    }

    private function calculateAvgTime($startColumn, $endColumn)
    {
        $durations = $this->OrderData->filter(function ($order) use ($startColumn, $endColumn) {
            return $order->$startColumn && $order->$endColumn;
        })->map(function ($order) use ($startColumn, $endColumn) {
            $startDate = Carbon::parse($order->$startColumn);
            $endDate = Carbon::parse($order->$endColumn);


            $diffInSeconds = $startDate->diffInSeconds($endDate);
            \Log::info('Time Difference (seconds): ' . $diffInSeconds);
            return $diffInSeconds;
        });

        if ($durations->isEmpty()) {
            return '00:00:00';
        }

        $avgSeconds = $durations->avg();
        \Log::info('Average Time Difference (seconds): ' . $avgSeconds);

        // Handle durations longer than 24 hours
        $hours = floor($avgSeconds / 3600);
        $minutes = floor(($avgSeconds % 3600) / 60);
        $seconds = $avgSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }



}
