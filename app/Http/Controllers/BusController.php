<?php

namespace App\Http\Controllers;

use App\Services\BusService;
use Illuminate\Http\Request;
use App\Http\Requests\ViewBus;
use App\Http\Requests\UpdateBus;
use App\Http\Requests\DeleteBus;
use App\Http\Requests\RegisterBus;
use App\Transformers\BusTransformer;

class BusController extends ApiController
{
    protected $busService;

    /**
     * BusController constructor.
     *
     * @param BusService $busService
     */
    public function __construct(BusService $busService)
    {
        parent::__construct();

        $this->busService = $busService;

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
        return $this->respond($this->transform($this->busService->all($request)));
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
        $bus = $this->busService->create($request);

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
        $bus = $this->busService->updateName($request, $id);

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
        if ($this->busService->delete($id)) {
            return $this->respondWithMessage();
        } else {
            return $this->respondWithError('Unable to delete bus');
        }
    }

    /**
     * Get the bus arrival time
     *
     * @param ViewBus $request
     * @param $id
     * @return mixed
     */
    public function arrival(ViewBus $request, $id)
    {
        $result = $this->busService->arrival($id);

        return $this->respond($result);
    }
}
