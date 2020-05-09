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
     * Get all the bus stop from LTA
     *
     * @return array
     */
    private function getBusStops()
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
     * Populate bus stops
     *
     * @return BusStop[]|\Illuminate\Database\Eloquent\Collection
     */
    public function populate()
    {
        // delete all bus stops
        BusStop::truncate();
        // get all the bus stops from LTA
        $result = $this->getBusStops();
        // save all bus stops to DB
        BusStop::insert($result);

        return BusStop::all();
    }

    /**
     * Get nearby bus stops
     *
     * @param float $latitude
     * @param float $longitude
     * @return BusStop[]|\Illuminate\Database\Eloquent\Collection
     */
    public function nearby($latitude = 0.00, $longitude = 0.00)
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

        return $result;
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
}