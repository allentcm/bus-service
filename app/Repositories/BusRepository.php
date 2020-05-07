<?php

namespace App\Repositories;

use App\Bus;
use App\User;

class BusRepository
{
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
}