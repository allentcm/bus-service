<?php

namespace App\Http\Middleware;

use Closure;

class CheckUpdateBus
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
        // reject if there are other fields then name in the request
        $fields = array_keys($request->all());
        if (count($fields) > 1 || $fields[0] !== 'name') {
            return abort(403);
        }

        return $next($request);
    }
}
