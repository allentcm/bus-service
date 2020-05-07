<?php

namespace App\Policies;

use App\Bus;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the bus.
     *
     * @param  \App\User  $user
     * @param  \App\Bus  $bus
     * @return mixed
     */
    public function view(User $user, Bus $bus)
    {
        // make sure the user own the bus
        if ($user->id != $bus->user_id) {
            return false;
        }
        
        return true;
    }

    /**
     * Determine whether the user can create buses.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the bus.
     *
     * @param  \App\User  $user
     * @param  \App\Bus  $bus
     * @return mixed
     */
    public function update(User $user, Bus $bus)
    {
        // make sure the user own the bus
        if ($user->id != $bus->user_id) {
            return false;
        }
        
        return true;
    }

    /**
     * Determine whether the user can delete the bus.
     *
     * @param  \App\User  $user
     * @param  \App\Bus  $bus
     * @return mixed
     */
    public function delete(User $user, Bus $bus)
    {
        // make sure the user own the bus
        if ($user->id != $bus->user_id) {
            return false;
        }
        
        return true;
    }
}
