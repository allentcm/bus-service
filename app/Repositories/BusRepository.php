<?php

namespace App\Repositories;

use App\Bus;
use App\User;

class BusRepository
{
    /**
     * Create bus
     *
     * @param User $user
     * @return Bus
     * @throws Exception
     */
    public function create(User $user, array $attributes)
    {
        $bus = Bus::forceCreate($attributes);
        $user->buses()->save((object) $bus);
        return $user->buses;
    }

    /**
     * Update user's buses
     *
     * @param Bus $bus
     * @param string $name New name for the bus
     * @return Bus
     * @throws Exception
     */
    public function updateName(Bus $bus, string $name)
    {
        $bus->name = $name;
        $bus->save();
        return $bus;
    }

    /**
     * Delete user's buses
     * 
     * @param Bus $bus
     * @return boolean true if successful
     * @throws Exception
     */
    public function delete(Bus $bus)
    {
        return $bus->delete();
    }
}