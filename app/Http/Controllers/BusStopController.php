<?php

namespace App\Http\Controllers;

use App\BusStop;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Repositories\BusStopRepository;

class BusStopController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusStopRepository $busStops)
    {
        $this->busStops = $busStops;
    }

    /**
     * Get all bus stops
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        if (BusStop::all()->count() <= 0) {
            $client = new Client();
            $res = $client->request('GET', 'http://datamall2.mytransport.sg/ltaodataservice/BusStops', [
                'headers' => [
                    'AccountKey' => 'mFif1WYVRouNYFiWTZKqZQ==',
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
            $this->busStops->persist($collection->toArray());
        }

        
        return BusStop::all();
    }

    /**
     * Delete all bus stops and repopulate
     *
     * @return \Illuminate\Http\Response
     */
    public function refresh()
    {
        if (BusStop::all()->count() > 0) {
            BusStop::truncate();
            $client = new Client();
            $res = $client->request('GET', 'http://datamall2.mytransport.sg/ltaodataservice/BusStops', [
                'headers' => [
                    'AccountKey' => 'mFif1WYVRouNYFiWTZKqZQ==',
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
            $this->busStops->persist($collection->toArray());
        }

        return BusStop::all();
    }

    /**
     * Get nearby bus stops
     *
     * @return \Illuminate\Http\Response
     */
    public function nearby(Request $request)
    {
        $latitude = $request->latitude;
        $longitude = $request->longitude;

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
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function services(Request $request, $code)
    {
        $client = new Client();
        $res = $client->request('GET', 'http://datamall2.mytransport.sg/ltaodataservice/BusArrivalv2?BusStopCode=' . $code, [
            'headers' => [
                'AccountKey' => 'mFif1WYVRouNYFiWTZKqZQ==',
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
