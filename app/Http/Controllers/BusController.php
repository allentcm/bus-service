<?php

namespace App\Http\Controllers;

use App\Bus;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterBus;
use App\Repositories\BusRepository;

class BusController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusRepository $buses)
    {
        $this->buses = $buses;
    }

    /**
     * Get all bus stops
     * 
     * @param Request $request
     * @param int $id ID for the bus
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
     * @param RegisterBus $request Request with form validation
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterBus $request)
    {
        $user = $request->user();
        // prepare bus attribute for creation
        $attributes = [
            'user_id' => $user->id,
            'bus_stop_code' => $request->bus['bus_stop_code'],
            'service_no' => $request->bus['service_no'],
            'operator' => $request->bus['operator'],
            'name' => $request->name,
            'origin_code' => $request->bus['origin_code'],
            'destination_code' => $request->bus['destination_code'],
        ];
        // create user's bus
        return $this->buses->create($user, $attributes);
    }

    /**
     * Update a bus for user
     *
     * @param RegisterBus $request Request with form validation
     * @param int $id ID for the bus
     * @return \Illuminate\Http\Response
     */
    public function update(RegisterBus $request, $id)
    {
        // chekc if the bus exist
        $bus = Bus::find($id);
        if ($bus == null) {
            return $this->respondNotFound();
        }
        // check for user authorization
        $this->authorize('update', $bus);
        // update user's bus
        $user = $request->user();
        $bus = $this->buses->updateName($bus, $request->name);
        if ($bus == null) {
            return $this->respondNotFound();
        }
        return $bus;
    }

    /**
     * Delete the bus from database.
     * 
     * @param Request $request
     * @param int $id ID for the bus
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // chekc if the bus exist
        $bus = Bus::find($id);
        if ($bus == null) {
            return $this->respondNotFound();
        }
        // check for user authorization
        $this->authorize('delete', $bus);
        // delete user's bus
        if ($this->buses->delete($bus)) {
            return $this->respondWithMessage();
        } else {
            return $this->respondWithError('Unable to delete bus');
        }
    }

}
