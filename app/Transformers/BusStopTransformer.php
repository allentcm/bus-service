<?php

namespace App\Transformers;

use App\BusStop;
use League\Fractal\TransformerAbstract;

class BusStopTransformer extends TransformerAbstract
{
    /**
     * Transform bus stop data
     *
     * @param BusStop $busStop
     * @return array
     */
    public function transform(BusStop $busStop)
    {
        return [
            'id' => $busStop->id,
            'bus_stop_code' => $busStop->bus_stop_code,
            'road_name' => $busStop->road_name,
            'description' => $busStop->description,
            'latitude' => $busStop->latitude,
            'longitude' => $busStop->longitude,
        ];
    }
}