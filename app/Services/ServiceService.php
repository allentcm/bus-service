<?php

namespace App\Services;

use GuzzleHttp\Client;

class ServiceService
{
    protected $bus, $url, $appKey;

    /**
     * ServiceService constructor.
     */
    public function __construct()
    {
        $this->url = env('LTA_URL', '');
        $this->appKey = env('LTA_APP_KEY', '');
    }

    /**
     * Get bus stop's services
     *
     * @param string $code Bus stop code
     * @return mixed
     * @throws \Exception
     */
    public function services($code)
    {
        return $this->getServices($code);
    }

    /**
     * Get bus stop's services
     *
     * @param string $code Targeted bus stop code
     * @return \Illuminate\Support\Collection Collection of services
     */
    private function getServices($code)
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

        return $collection;
    }
}