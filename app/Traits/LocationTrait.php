<?php
namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

trait LocationTrait
{
  public  function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371): float|int
  {
        // Convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius; // Distance in kilometers
    }

    public function getTravelTime($originLat, $originLng, $destinationLat, $destinationLng): ?array
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json";

        $response = Http::get($url, [
            'origins' => "$originLat,$originLng",
            'destinations' => "$destinationLat,$destinationLng",
            'key' => env('GOOGLE_API_KEY'),
        ]);

        $data = $response->json();

        if ($data['status'] == 'OK') {
            return [
                'distance' => $data['rows'][0]['elements'][0]['distance']['value'], // Distance in km or meters
                'duration' => $data['rows'][0]['elements'][0]['duration']['value'], // Travel time
            ];
        } else {
            return [
                'distance' => 0 ,
                'duration' => 0
            ];
        }
    }
}
