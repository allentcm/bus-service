<?php

namespace App\Repositories;

use App\Bus;
use App\User;
use GuzzleHttp\Client;

class BusRepository
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->url = env('LTA_URL', '');
        $this->appKey = env('LTA_APP_KEY', '');
    }

    /**
     * Get bus for user
     *
     * @param User $user
     * @return mixed
     */
    public function all(User $user)
    {
        return $user->buses();
    }

    /**
     * Create bus for user
     *
     * @param User $user
     * @param array $attributes
     * @return mixed
     */
    public function create(User $user, array $attributes)
    {
        $bus = Bus::create($attributes);
        $user->buses()->save($bus);
        return $user->buses()->where('id', $bus->id)->first();
    }

    /**
     * Update bus name
     *
     * @param Bus $bus
     * @param string $name
     * @return Bus
     */
    public function updateName(Bus $bus, $name)
    {
        $bus->name = $name;
        $bus->save();
        return $bus;
    }

    /**
     * @param Bus $bus
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Bus $bus)
    {
        return $bus->delete();
    }

    /**
     * Get the bus arrival time at bus stop from LTA
     *
     * @return array
     */
    public function arrival(Bus $bus)
    {
        $client = new Client();
        $res = $client->request('GET', $this->url
                . '/BusArrivalv2?BusStopCode=' . $bus->bus_stop_code
                . '&ServiceNo=' . $bus->service_no, [
            'headers' => [
                'AccountKey' => $this->appKey,
                'Accept'     => 'application/json'
            ]
        ]);

        $result = json_decode($res->getBody());
        if ($result != null && isset($result->Services) && count($result->Services) > 0) {
            return $result->Services[0]->NextBus->EstimatedArrival;
        } else {
            return '';
        }
    }
}