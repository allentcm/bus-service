<?php

namespace App;

use App\BusStop;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['user_id'];

    /**
     * A bus belong to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getBusStop()
    {
        $busStop = BusStop::where('bus_stop_code', $this->bus_stop_code)->first();

        return $busStop;
    }

}
