<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bus;
use GuzzleHttp\Client;

class BusStopController extends Controller
{
    /**
     * Get all bus stops
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $client = new Client();
        $res = $client->request('GET', 'http://datamall2.mytransport.sg/ltaodataservice/BusStops', [
            'headers' => [
                'AccountKey' => 'mFif1WYVRouNYFiWTZKqZQ==',
                'Accept'     => 'application/json'
            ]
        ]);

        $result = $res->getBody();
        return $result;
    }
}
