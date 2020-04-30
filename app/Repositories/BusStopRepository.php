<?php

namespace App\Repositories;

use App\BusStop;

class BusStopRepository
{
    /**
     * Create bag
     *
     * @param User $user
     * @throws Exception
     */
    public function persist(array $busStops)
    {
        BusStop::insert($busStops);
    }
}