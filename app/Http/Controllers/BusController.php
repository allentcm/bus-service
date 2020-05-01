<?php

namespace App\Http\Controllers;

use App\Bus;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterBus;

class BusController extends Controller
{
    /**
     * Get all bus stops
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $user = $request->user();

        return $user->buses;
    }

    /**
     * Add a bus for user
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterBus $request)
    {
        $user = $request->user();
        $attributes = [
            'user_id' => $user->id,
            'bus_stop_code' => $request->bus['bus_stop_code'],
            'service_no' => $request->bus['service_no'],
            'operator' => $request->bus['operator'],
            'name' => $request->name,
            'origin_code' => $request->bus['origin_code'],
            'destination_code' => $request->bus['destination_code'],
        ];
        $bus = Bus::forceCreate($attributes);
        $user->buses()->save((object) $bus);
        return $user->buses;
    }
}
