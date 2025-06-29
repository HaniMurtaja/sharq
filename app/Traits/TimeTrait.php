<?php

namespace App\Traits;

use App\Settings\GeneralSettings;
use Carbon\Carbon;

trait TimeTrait
{
    public function restrictedTime(): bool
    {
        $now = Carbon::now('Asia/Riyadh');

        $startTime = Carbon::today('Asia/Riyadh')->setHour(4)->setMinute(15)->setSecond(0);
        $endTime   = Carbon::today('Asia/Riyadh')->setHour(14)->setMinute(0)->setSecond(0);

        // Check if the current time is between 2 AM and 7 AM
        if ($now->between($startTime, $endTime)) {
            return true;
        }
        return false;
    }

    public function isWithinBusinessHours()
    {
        $settings = new GeneralSettings();
        if (! $settings) {
            return false;
        }
        $now = Carbon::parse("2025-06-02 00:39:58");

        $start            = $settings->business_hours['start_time']; // وقت البداية
        $end              = $settings->business_hours['end_time'];   // وقت النهاية
        $shiftEndTomorrow = $settings->shift_end_tomorrow;           // هل ينتهي الدوام في اليوم التالي؟
        $startTime        = Carbon::createFromFormat('H:i', $start);
        $endTime          = Carbon::createFromFormat('H:i', $end);

        $startDateTime    = $now->copy()->setTimeFrom($startTime);
        if ($shiftEndTomorrow) {
            $endDateTime = $now->copy()->addDay()->setTimeFrom($endTime);
        } else {
            $endDateTime = $now->copy()->setTimeFrom($endTime);
        }

        $currentTime = Carbon::createFromFormat('H:i', $now->format('H:i'));
        if ($endTime->lessThan($startTime)) {
            if ($currentTime->greaterThanOrEqualTo($startTime) || $currentTime->lessThan($endTime)) {

                return [
                    'nowz'                     => $now->toDateTimeString(),
                    'is_within_business_hours' => true,
                    'start_time'               => $startDateTime->toDateTimeString(),
                    'end_time'                 => $endDateTime->toDateTimeString(),
                ];
            }
        } else {
            if ($currentTime->greaterThanOrEqualTo($startTime) && $currentTime->lessThanOrEqualTo($endTime)) {
                $startDateTime = $now->copy()->setTimeFrom($startTime);
                $endDateTime   = $now->copy()->setTimeFrom($endTime);
                return [
                    'nowzzs'                   => $now->toDateTimeString(),
                    'is_within_business_hours' => true,
                    'start_time'               => $startDateTime->toDateTimeString(),
                    'end_time'                 => $endDateTime->toDateTimeString(),
                ];
            }
        }
        return false;
    }
}
