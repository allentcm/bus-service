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
}
