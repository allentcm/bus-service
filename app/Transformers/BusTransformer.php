<?php

namespace App\Transformers;

use App\Bus;
use League\Fractal\TransformerAbstract;

class BusTransformer extends TransformerAbstract
{
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
}