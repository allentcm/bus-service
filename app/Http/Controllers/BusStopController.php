<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use App\Repositories\BusStopRepository;
use App\Transformers\BusStopTransformer;
use Illuminate\Pagination\LengthAwarePaginator;

class BusStopController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusStopRepository $busStops)
    {
        parent::__construct();

        $this->busStops = $busStops;

        $this->setTransformer(new BusStopTransformer());
    }

    /**
     * Get all bus stops base on user proximity
     *
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function all(Request $request)
    {
        $nearestBusStops = $this->busStops->nearby($request->latitude, $request->longitude);
        $paginator = $this->paginate($nearestBusStops, 10, request('page'), ['path' => request()->path()]);

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
        $busStops = $this->busStops->populate();
        $paginator = $this->paginate($busStops, 10, request('page'), ['path' => request()->path()]);

        return $this->respond($this->transform($paginator));
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
