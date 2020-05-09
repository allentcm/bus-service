<?php

namespace App\Http\Middleware;

use Closure;

class CheckViewBusStop
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
        $pattern1 = [
            'latitude',
            'longitude'
        ];
        $pattern2 = [
            'latitude',
            'longitude',
            'page'
        ];
        $length = count($request->all());

        $fields = array_keys($request->all());
        if (count(array_diff($length === 2 ? $pattern1 : $pattern2, $fields)) > 0 ||
            count(array_diff($fields, $length === 2 ? $pattern1 : $pattern2)) > 0
        ) {
            return abort(403, trans('errors.wrong_parameters'));
        }

        return $next($request);
    }
}
