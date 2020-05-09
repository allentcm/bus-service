<?php

namespace App\Services;

use App\Bus;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Repositories\Repository;
use App\Http\Requests\UpdateBus;
use App\Http\Requests\RegisterBus;

class BusService
{
    protected $bus, $url, $appKey;

    /**
     * BusService constructor.
     *
     * @param Bus $bus
     */
    public function __construct(Bus $bus)
    {
        $this->bus = new Repository($bus);

        $this->url = env('LTA_URL', '');
        $this->appKey = env('LTA_APP_KEY', '');
    }

    /**
     * Get bus for user
     *
     * @param Request $request
     * @return mixed
     */
    public function all(Request $request)
    {
        // make sure the user get his own buses
        return $request->user()->buses;
    }

    /**
     * Create bus for user
     *
     * @param RegisterBus $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(RegisterBus $request)
    {
        // create user's bus
        $bus = $this->bus->create($request->all());
        $request->user()->buses()->save($bus);

        return $this->bus->show($bus->id);
    }

    /**
     * Update bus name
     *
     * @param UpdateBus $request
     * @param $id
     * @return mixed
     */
    public function updateName(UpdateBus $request, $id)
    {
        $attributes = ['name' => $request->name];
        $this->bus->update($attributes, $id);

        return $this->bus->show($id);
    }

    /**
     * Delete bus
     *
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        return $this->bus->delete($id);
    }

    /**
     *  Get the bus arrival time at bus stop from LTA
     *
     * @param $id
     * @return string
     */
    public function arrival($id)
    {
        $bus = $this->bus->show($id);

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