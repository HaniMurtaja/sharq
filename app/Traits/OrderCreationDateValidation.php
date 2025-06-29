<?php



namespace App\Traits;

use Carbon\Carbon;
use App\Models\ClientDetail;
use App\Models\SpecialBusinessHours;
use App\Settings\GeneralSettings;

trait OrderCreationDateValidation
{
    function isWithinBusinessHours($client_id = null)
    {

        $settings = new GeneralSettings();
        if (!$settings) {
            return false;
        }
        $now = Carbon::now('Asia/Riyadh');
        $clientSchedule = SpecialBusinessHours::where('client_id', $client_id)
            ->whereDate('start', '<=', $now)
            ->whereDate('end', '>=', $now)
            ->orderbydesc('id')
            ->first();
        if ($clientSchedule != null) {
            return true;
        }
        //dd($clientSchedule,$client_id);


        $start = $settings->business_hours['start_time'];
        $end = $settings->business_hours['end_time'];
        $shiftEndTomorrow = $settings->shift_end_tomorrow;


        $currentTime = Carbon::createFromFormat('H:i', $now->format('H:i'));
        $startTime = Carbon::createFromFormat('H:i', $start);
        $endTime = Carbon::createFromFormat('H:i', $end);

        // echo "Current Time: " . $currentTime->format('H:i') . "\n";
        // echo "Start Time: " . $startTime->format('H:i') . "\n";
        // echo "End Time: " . $endTime->format('H:i') . "\n";
        // echo "Shift Ends Tomorrow: $shiftEndTomorrow\n";
        if ($endTime->lessThan($startTime)) { //  إذا كانت نهاية الدوام في اليوم التالي
            if ($currentTime->greaterThanOrEqualTo($startTime) || $currentTime->lessThan($endTime)) {

                return true;
            }
        } else { //  إذا كان الدوام في نفس اليوم فقط
            if ($currentTime->greaterThanOrEqualTo($startTime) && $currentTime->lessThanOrEqualTo($endTime)) {

                return true;
            }
        }

        return false;
    }
    function getBusinessHoursIfNowWithinRange($client_id = null)
    {
        $settings = new GeneralSettings();
        if (!$settings) {
            return null;
        }

        $now = Carbon::now('Asia/Riyadh');
        //$now = Carbon::parse('2025-06-12 7:00:00');

        $clientSchedule = SpecialBusinessHours::where('client_id', $client_id)
            ->whereDate('start', '<=', $now)
            ->whereDate('end', '>=', $now)
            ->orderByDesc('id')
            ->first();

        if ($clientSchedule) {
            return [
                'now' => $now->toDateTimeString(),
                'start' => Carbon::parse($clientSchedule->start)->toDateTimeString(),
                'end' => Carbon::parse($clientSchedule->end)->toDateTimeString(),
            ];
        }

        $start = $settings->business_hours['start_time'];
        $end = $settings->business_hours['end_time'];
        $shiftEndTomorrow = $settings->shift_end_tomorrow;

        $startTime = Carbon::parse($now->format('Y-m-d') . ' ' . $start);
        $endTime = Carbon::parse($now->format('Y-m-d') . ' ' . $end);


        if ($shiftEndTomorrow && $endTime->lessThanOrEqualTo($startTime)) {

            if ($now->greaterThanOrEqualTo($startTime)) {
                $endTime->addDay();
            } else {

                $startTime->subDay();
            }
        }

        return [
            'now' => $now->toDateTimeString(),
            'start' => $startTime->toDateTimeString(),
            'end' => $endTime->toDateTimeString(),
        ];
    }




    function isWithinBusinessHoursMain($client_id = null)
    {

        $settings = new GeneralSettings();
        if (!$settings) {
            return false;
        }
        $now = Carbon::now('Asia/Riyadh');
        $client = SpecialBusinessHours::where('client_id', '243')
            ->where('start', '<=', $now)
            ->where('end', '>=', $now)
            ->orderbydesc('id')
            ->first();


        $start = $settings->business_hours['start_time']; // وقت البداية
        $end = $settings->business_hours['end_time']; // وقت النهاية
        $shiftEndTomorrow = $settings->shift_end_tomorrow; // هل ينتهي الدوام في اليوم التالي؟

        // الحصول على الوقت الحالي بتوقيت الرياض

        $currentTime = Carbon::createFromFormat('H:i', $now->format('H:i'));
        $startTime = Carbon::createFromFormat('H:i', $start);
        $endTime = Carbon::createFromFormat('H:i', $end);

        // echo "Current Time: " . $currentTime->format('H:i') . "\n";
        // echo "Start Time: " . $startTime->format('H:i') . "\n";
        // echo "End Time: " . $endTime->format('H:i') . "\n";
        // echo "Shift Ends Tomorrow: $shiftEndTomorrow\n";
        if ($endTime->lessThan($startTime)) { // ✅ إذا كانت نهاية الدوام في اليوم التالي
            if ($currentTime->greaterThanOrEqualTo($startTime) || $currentTime->lessThan($endTime)) {
                //  dd('true'); // Debug قبل الإرجاع
                return true;
            }
        } else { // ✅ إذا كان الدوام في نفس اليوم فقط
            if ($currentTime->greaterThanOrEqualTo($startTime) && $currentTime->lessThanOrEqualTo($endTime)) {
                //  dd('true'); // Debug قبل الإرجاع
                return true;
            }
        }
        // dd('false'); // Debug قبل الإرجاع
        return false;
    }
    function isWithinBusinessHoursOld($client_id = null)
    {
        $client = ClientDetail::where('user_id', $client_id)->first();
        //    dd($client);
        $settings = new GeneralSettings();

        try {

            $startTime = Carbon::createFromFormat('H:i', $settings->business_hours['start_time']);
            $endTime = Carbon::createFromFormat('H:i', $settings->business_hours['end_time']);

            if ($settings->shift_end_tomorrow == 1) {
                $endTime = Carbon::tomorrow()->setTimeFromTimeString($settings->business_hours['end_time']);
            }
        } catch (\Exception $e) {

            $startTime = Carbon::today()->startOfDay();
            $endTime = Carbon::today()->endOfDay();
        }

        //    dd($startTime, $endTime);
        $now = Carbon::now();
        if ($now->between($startTime, $endTime)) {
            // dd(9);
            return true;
        }


        $specialBusinessHours = $settings->special_business_hours;

        $specialStartTime = Carbon::parse($specialBusinessHours['start_time']);
        $specialEndTime = Carbon::parse($specialBusinessHours['end_time']);


        if ($now->between($specialStartTime, $specialEndTime) && $client->has_special_business_hours == 1) {
            // dd(99);
            return true;
        }

        // dd(999);
        return false;
    }
}
