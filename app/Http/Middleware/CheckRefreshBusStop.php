<?php

namespace App\Http\Middleware;

use Closure;

class CheckRefreshBusStop
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
        if (count($request->all()) > 0) {
            // reject if there is field in the request
            return abort(403, trans('errors.extra_parameter'));
        }

        return $next($request);
    }
}
