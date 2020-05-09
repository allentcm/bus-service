<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class CheckViewBus
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
        $validator = Validator::make($request->route()->parameters, [
            'id' => 'required|integer',
        ]);

        if (count($request->all()) > 0) {
            // reject if there is field in the request
            return abort(403, trans('errors.extra_parameter'));
        } else if ($validator->fails()) {
            // reject if fail validation
            return abort(403, trans('errors.wrong_parameter_type'));
        }

        return $next($request);
    }
}
