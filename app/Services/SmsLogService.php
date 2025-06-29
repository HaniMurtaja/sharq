<?php

namespace App\Services;

use App\Models\SmsLog;

class SmsLogService
{
    public function createLog(string $number, string $message,$requestFields, ?string $correlationId = null, ?string $response_body = null): SmsLog
    {
        try {

        return SmsLog::create([
            'number'          => $number,
            'message'         => $message,
            'correlation_id'  => $correlationId,
            'request_fields'  => $requestFields,
            'response_body'   => $response_body,
        ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function updateResponse(SmsLog $smsLog,  $responseBody): void
    {
        try {
        $smsLog->update([
            'response_body' => $responseBody ?? [],
        ]);
        } catch (\Throwable $th) {
           // throw $th;
        }
    }
}
