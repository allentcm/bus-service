<?php

namespace App\Repositories;

use App\BusStop;
use GuzzleHttp\Client;

class BusStopRepository
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->url = env('LTA_URL', '');
        $this->appKey = env('LTA_APP_KEY', '');
    }

    /**
     * Persist bus stops
     *
     * @param array $busStops
     * @return array All the bus stops from LTA
     * @throws Exception
     */
    public function getBusStops()
    {
        $client = new Client();
        $res = $client->request('GET', $this->url . '/BusStops', [
            'headers' => [
                'AccountKey' => $this->appKey,
                'Accept'     => 'application/json'
            ]
        ]);

        $result = json_decode($res->getBody());
        $collection = collect($result->value);
        $collection->transform(function ($item, $key) {
            $array = (array) $item;
            $newArray = [];
            array_walk($array, function ($value, $key) use (&$newArray) {
                $newArray[snake_case($key)] = $value;
            });
            return $newArray;
        });

        return $collection->toArray();
    }

    /**
     * Persist bus stops
     *
     * @param array $busStops
     * @throws Exception
     */
    public function persist(array $busStops)
    {
        BusStop::insert($busStops);
    }

    /**
     * Get nearby bus stops
     *
     * @param float $latitude Latitude for reference location
     * @param float $longitude Longitude for reference location
     * @return array Array for all the bus stops arranged according to proximity
     */
    public function nearby(float $latitude = 0.00, float $longitude = 0.00)
    {
        $busStops = BusStop::all();
        $result = $busStops->sort(function ($a, $b) use ($latitude, $longitude) {
            $distanceA = $this->distance($latitude, $longitude, $a->latitude, $a->longitude);
            $distanceB = $this->distance($latitude, $longitude, $b->latitude, $b->longitude);
            if ($distanceA == $distanceB) {
                return 0;
            }
            return ($distanceA < $distanceB) ? -1 : 1;
        });
        $array = array_values($result->toArray());
        return $array;
    }

    /**
     * Get distance from the reference point
     *
     * @param float $latRef Latitude for reference location
     * @param float $longRef Longitude for reference location
     * @param float $latPos Latitude for target location
     * @param float $longPos Longitude for target location
     * @return float $mile Distance in mile
     */
    private function distance($latRef, $longRef, $latPos, $longPos)
    {
        $long1 = deg2rad($longRef);
        $long2 = deg2rad($longPos);
        $lat1 = deg2rad($latRef);
        $lat2 = deg2rad($latPos);
        //Haversine Formula
        $dlong = $long2 - $long1;
        $dlati = $lat2 - $lat1;
        $val = pow(sin($dlati/2),2)+cos($lat1)*cos($lat2)*pow(sin($dlong/2),2);
        $res = 2 * asin(sqrt($val));
        $radius = 3958.756;
        return ($res * $radius);
    }

    /**
     * Get bus stop's services
     *
     * @param string $code Code for the bust stop
     * @param array All the bus services for this bus stop
     */
    public function services($code)
    {
        $client = new Client();
        $res = $client->request('GET', $this->url . '/BusArrivalv2?BusStopCode=' . $code, [
            'headers' => [
                'AccountKey' => $this->appKey,
                'Accept'     => 'application/json'
            ]
        ]);

        $result = json_decode($res->getBody());
        $collection = collect($result->Services);
        $collection->transform(function ($item, $key) {
            $array = (array) $item;
            $newArray = [];
            array_walk($array, function ($value, $key) use (&$newArray) {
                if (is_object($value)) {
                    $newValueArray = [];
                    $valueArray = (array) $value;
                    array_walk($valueArray, function ($value, $key) use (&$newValueArray) {
                        $newValueArray[snake_case($key)] = $value;
                    });
                    $newArray[snake_case($key)] = $newValueArray;
                } else {
                    $newArray[snake_case($key)] = $value;
                }
            });
            return $newArray;
        });

        return $collection->toArray();
    }
}