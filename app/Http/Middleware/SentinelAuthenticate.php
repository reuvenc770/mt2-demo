<?php

namespace App\Http\Middleware;

use Closure;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Laracasts\Flash\Flash;
use App\Facades\UserEventLog;
class SentinelAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $action = $request->route()->getAction()['as'];
        if (!Sentinel::check()) {
            UserEventLog::insertCustomRequest(0,str_replace(".","/",$action),$request->getMethod(),\App\Models\UserEventLog::UNAUTHORIZED);
            if ($request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                Flash::warning('Please login to perform this action');
                return redirect()->guest('login');
            }
        }

        return $next($request);
    }
}
