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
        // make sure the user get his own buses
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

    /**
     * Update a bus for user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(RegisterBus $request, $id)
    {
        $bus = Bus::find($id);
        $this->authorize('update', $bus);
        // make sure the user own the bus
        $user = $request->user();
        $bus = $user->buses()->where('id', $id)->first();
        $bus->name = $request->name;
        $bus->save();
        return $bus;
    }

    /**
     * Destroy bus
     *
     * Delete the bus from database.
     *
     * @param int $id ID for the service
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $bus = Bus::find($id);
        $this->authorize('delete', $bus);
        // make sure the user own the bus
        $user = $request->user();
        $bus = $user->buses()->where('id', $id)->first();
        if ($bus == null) {
            return [
                'error' => 'Item not found'
            ];
        }
        $bus->delete();
    }

}
