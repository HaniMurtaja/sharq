<?php
namespace App\Http\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class MapDistanceCalculator{

    public function distanceMatrix($request)
    {

        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $request->input('origins'),
            'destinations' => $request->input('destinations'),
            'mode' => $request->input('mode')??"driving",
            'key' => $request->key??config('services.google.api_key'),
        ]);
// dd($response['rows'][0]['elements'][0]);

        $response = [
            "status" => $response['rows'][0]['elements'][0]['status'],
            "distance" => $response['rows'][0]['elements'][0]['distance']['text'],
            "duration" => $response['rows'][0]['elements'][0]['duration']['text']
        ];

        return $response;


    }
    public function distanceMatrixNew($request)
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $request->input('origins'),
            'destinations' => $request->input('destinations'),
            'mode' => $request->input('mode') ?? "driving",
            'key' => $request->key ?? config('services.google.api_key'),
        ]);

        // تحويل الاستجابة إلى array
        $response = $response->json();

        // تحقق من وجود البيانات المطلوبة
        if (
            isset($response['rows'][0]['elements'][0]['status']) &&
            $response['rows'][0]['elements'][0]['status'] === 'OK'
        ) {
            $response = [
                "status" => $response['rows'][0]['elements'][0]['status'],
                "distance" => $response['rows'][0]['elements'][0]['distance']['text'],
                "duration" => $response['rows'][0]['elements'][0]['duration']['text']
            ];
        } else {
            $response = [
                "status" => $response['rows'][0]['elements'][0]['status'] ?? 'ERROR',
                "distance" => null,
                "duration" => null
            ];
        }

        return $response;
    }

}










?>
