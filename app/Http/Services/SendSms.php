<?php

namespace App\Http\Services;

use App\Services\SmsLogService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class SendSms
{
    /**
     * Send a sms message to the given mobile.
     *
     * @param string $mobile
     * @param String $msg
     * @return \Illuminate\Http\JsonResponse
     */

    public static function unifonic($phone, $message)
    {
        $client = new \GuzzleHttp\Client();

        $body = [
            'AppSid' => env('UNIFONIC_APP_ID'),
            'SenderID' => env('UNIFONIC_SENDER_ID'),
            'Body' => $message,
            'Recipient' => $phone,
            'responseType' => 'JSON',
            'CorrelationID' => Str::uuid(),
            'baseEncode' => 'true',
            'statusCallback' => 'sent',
            'async' => 'false'
        ];


        $endpoint = 'https://el.cloud.unifonic.com/rest/SMS/messages';

        $client = new \GuzzleHttp\Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);

        $status_code = 200;
        $response_str = '';

        try {
            $response = $client->request('POST', $endpoint, ['json' => $body]);
            dd($response);
            return json_decode($response->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\ClientException $e) {

            $status_code = $e->getResponse()->getStatusCode();
            $response_str = $e->getResponse()->getBody()->getContents();

            Log::error($response_str);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            dd($e);
            $status_code = $e->getResponse()->getStatusCode();
            $response_str = $e->getResponse()->getBody()->getContents();
        }

        return $response_str;
    }

    public static function toSms($number, $msg)
    {
       // dd('dddd');
       $logger = new SmsLogService();
       $fields = [];
       $correlationId = (string) Str::uuid();
       $smsLog = null;
            $fields = array(
                'AppSid' => env('UNIFONIC_APP_ID'),
                'SenderID' => env('UNIFONIC_SENDER_ID'),
                'Body' => $msg,
                'Recipient' => $number,
                'responseType' => 'JSON',
                'CorrelationID' =>$correlationId,
                'baseEncode' => 'true',
                'statusCallback' => 'sent',
                'async' => 'false'
            );

            $smsLog = $logger->createLog(
                $number,
                $msg,
                $fields,
                $correlationId ?? null,
                null
            );
        try {

            if (App::environment('production')) {

                $postvars = http_build_query($fields);
                $endpoint = 'https://el.cloud.unifonic.com/rest/SMS/messages';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $endpoint,);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Content-Type: application/json",
                ));
                $response = curl_exec($ch);
               // dd( $response );


                if (curl_errno($ch)) {
                    $logger->updateResponse($smsLog, $response);
                    return response()->json(['status' => FALSE, 'message' => $ch]);
                }
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($httpcode != 200) {
                    $logger->updateResponse($smsLog, $response);
                    return response()->json(['status' => FALSE, 'message' => $httpcode]);
                }
                if($httpcode == 200){
                    $logger->updateResponse($smsLog, $response);
                    return response()->json(['status' => TRUE]);
                }

            }

        } catch (\Throwable $th) {
            if (!$smsLog) {
                $smsLog = $logger->createLog(
                    $number ?? 'unknown',
                    $msg ?? '',
                    $fields,
                    $correlationId,
                    null
                );
            }


            $logger->updateResponse($smsLog, [
                'exception' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString()
            ]);

            throw $th;
        }
    }
}
