<?php

namespace App\Http\Middleware;

use Closure;

class CheckBusFields
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // reject if there are other fields in the request
        $allowFiels = [
            'name',
            'bus_stop_code',
            'service_no',
            'operator',
            'origin_code',
            'destination_code'
        ];

        $fields = array_keys($request->all());
        if (count(array_diff($allowFiels, $fields)) > 0 ||
            count(array_diff($fields, $allowFiels)) > 0
        ) {
            return abort(403, trans('errors.wrong_parameters'));
        }

        return $next($request);
    }
}
