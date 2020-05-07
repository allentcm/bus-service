<?php

namespace App\Http\Controllers;

use App\Bus;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateBus;
use App\Http\Requests\DeleteBus;
use App\Http\Requests\RegisterBus;
use App\Repositories\BusRepository;
use App\Transformers\BusTransformer;

class BusController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusRepository $buses)
    {
        parent::__construct();

        $this->buses = $buses;

        $this->setTransformer(new BusTransformer());
    }

    /**
     * Get all bus belong to a user
     *
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function all(Request $request)
    {
        $user = $request->user();
        // make sure the user get his own buses
        return $this->respond($this->transform($user->buses));
    }

    /**
     * Add a bus for user
     *
     * @param RegisterBus $request
     * @return mixed
     * @throws \Exception
     */
    public function store(RegisterBus $request)
    {
        // create user's bus
        $bus = $this->buses->create($request->user(), $request->all());
        if ($bus != null) {
            return $this->respond($this->transform($bus));
        } else {
            return $this->respondWithError('Unable to create bus');
        }
    }

    /**
     * Update a bus for user
     *
     * @param UpdateBus $request
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function update(UpdateBus $request, $id)
    {
        // chekc if the bus exist
        $bus = Bus::find($id);
        if ($bus == null) {
            return $this->respondNotFound();
        }

        $bus = $this->buses->updateName($bus, $request->name);
        if ($bus != null) {
            return $this->respond($this->transform($bus));
        } else {
            return $this->respondWithError('Unable to create bus');
        }
    }

    /**
     * Delete the bus from database
     *
     * @param DeleteBus $request
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function destroy(DeleteBus $request, $id)
    {
        // check if the bus exist
        $bus = Bus::find($id);
        if ($bus == null) {
            return $this->respondNotFound();
        }

        // delete user's bus
        if ($this->buses->delete($bus)) {
            return $this->respondWithMessage();
        } else {
            return $this->respondWithError('Unable to delete bus');
        }
    }

}
