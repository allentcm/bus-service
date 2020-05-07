<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class ServiceTransformer extends TransformerAbstract
{
    /**
     * Transform bus service data
     *
     * @param array $service
     * @return array
     */
    public function transform(array $service)
    {
        return [
            'service_no' => $service['service_no'],
            'operator' => $service['operator'],
            'next_bus' => [
                'origin_code' => $service['next_bus']['origin_code'],
                'destination_code' => $service['next_bus']['destination_code'],
                'estimated_arrival' => $service['next_bus']['estimated_arrival'],
            ],
        ];
    }
}