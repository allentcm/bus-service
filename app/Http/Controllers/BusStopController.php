<?php

namespace App\Http\Controllers;

use App\Services\BusStopService;
use Illuminate\Support\Collection;
use App\Http\Requests\ViewBusStop;
use Illuminate\Pagination\Paginator;
use App\Transformers\BusStopTransformer;
use Illuminate\Pagination\LengthAwarePaginator;

class BusStopController extends ApiController
{
    protected $busStopService;

    /**
     * BusStopController constructor.
     *
     * @param BusStopService $busStopService
     */
    public function __construct(BusStopService $busStopService)
    {
        parent::__construct();

        $this->busStopService = $busStopService;

        $this->setTransformer(new BusStopTransformer());
    }

    /**
     * Get all bus stops base on user proximity
     *
     * @param ViewBusStop $request
     * @return mixed
     * @throws \Exception
     */
    public function all(ViewBusStop $request)
    {
        $nearestBusStops = $this->busStopService->nearby($request);
        $paginator = $this->paginate($nearestBusStops, 10, request('page'), ['path' => request()->getRequestUri()]);

        return $this->respond($this->transform($paginator));
    }

    /**
     * Delete all bus stops and re-populate
     *
     * @return mixed
     * @throws \Exception
     */
    public function refresh()
    {
        $busStops = $this->busStopService->populate();

        return $this->respond($this->transform($busStops));
    }

    /**
     * Setup pagination for collection
     *
     * @param $items
     * @param int $perPage
     * @param null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    private function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
