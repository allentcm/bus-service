<?php

namespace App\Transformers;

use App\Bus;
use League\Fractal\TransformerAbstract;

class BusTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'bus_stop',
    ];

    /**
     * Transform bus data
     *
     * @param Bus $bus
     * @return array
     */
    public function transform(Bus $bus)
    {
        return [
            'id' => $bus->id,
            'name' => $bus->name,
            'user_id' => $bus->user_id,
            'bus_stop_code' => $bus->bus_stop_code,
            'service_no' => $bus->service_no,
            'operator' => $bus->operator,
            'origin_code' => $bus->origin_code,
            'destination_code' => $bus->destination_code
        ];
    }

    /**
     * Include bus stop
     *
     * @param Bus $bus
     * @return \League\Fractal\Resource\Item
     */
    public function includeBusStop(Bus $bus)
    {
        $busStop = $bus->getBusStop();
        if ($busStop) {
            $transformer = new BusStopTransformer();
            return $this->item($busStop, $transformer, 'bus_stop');
        }
    }
}