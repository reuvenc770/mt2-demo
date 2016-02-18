<?php

namespace App\Http\Middleware;

use Closure;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Laracasts\Flash\Flash;
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
        if (!Sentinel::check()) {
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
