<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

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
        $validator = Validator::make($request->route()->parameters, [
            'id' => 'required|integer',
        ]);

        $fields = array_keys($request->all());

        if (count($fields) > 1 || $fields[0] !== 'name') {
            // reject if there are other fields then name in the request
            return abort(403, trans('errors.wrong_parameters'));
        } else if ($validator->fails()) {
            // reject if fail validation
            return abort(403, trans('errors.wrong_parameter_type'));
        }

        return $next($request);
    }
}
