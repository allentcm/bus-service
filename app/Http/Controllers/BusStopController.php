<?php

namespace App\Http\Controllers;

use App\BusStop;
use Illuminate\Http\Request;
use App\Repositories\BusStopRepository;

class BusStopController extends ApiController
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
    public function all(Request $request)
    {
        return $this->busStops->nearby($request->latitude, $request->longitude);
    }

    /**
     * Delete all bus stops and repopulate
     *
     * @return \Illuminate\Http\Response
     */
    public function refresh()
    {
        // check is bus stops already populated
        if (BusStop::all()->count() > 0) {
            // delete all bus stops
            BusStop::truncate();
            // get all the bus stops from LTA
            $resut = $this->busStops->getBusStops();
            // save all bus stops to DB
            $this->busStops->persist($resut);
        }

        return BusStop::all();
    }

    /**
     * Get bus stop's services
     *
     * @param string $code Code for the bust stop
     * @return \Illuminate\Http\Response
     */
    public function services($code)
    {
        return $this->busStops->services($code);
    }
}
