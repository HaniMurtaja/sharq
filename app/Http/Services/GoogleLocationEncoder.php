<?php
namespace App\Http\Services;

use GuzzleHttp\Client;

class GoogleLocationEncoder
{
    protected $client;
    protected $apiKey;
    protected $location;

    public function __construct($address)
    {
        $this->client = new Client();
        $this->apiKey = env('GOOGLE_MAPS_API_KEY'); 
        $this->location = $this->fetchCoordinates($address);
    }

    protected function fetchCoordinates($address)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json';
        $response = $this->client->get($url, [
            'query' => [
                'address' => $address,
                'key' => $this->apiKey,
            ]
        ]);

        $data = json_decode($response->getBody()->getContents());

        if ($data->status == 'OK') {
            return $data->results[0]->geometry->location;
        }

        return null;
    }

    public function getLatitude()
    {
        return $this->location ? $this->location->lat : null;
    }

    public function getLongitude()
    {
        return $this->location ? $this->location->lng : null;
    }
}
