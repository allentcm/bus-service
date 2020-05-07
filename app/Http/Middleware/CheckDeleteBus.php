<?php

namespace App\Http\Middleware;

use Closure;

class CheckDeleteBus
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
        // reject if there is field in the request
        if (count($request->all()) > 0) {
            return abort(403);
        }

        return $next($request);
    }
}
