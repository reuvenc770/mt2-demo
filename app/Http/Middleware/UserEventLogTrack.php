<?php

namespace App\Http\Middleware;

use Closure;

use App\Facades\UserEventLog;
class UserEventLogTrack
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
        return $next($request);
    }

    public function terminate($request,$response) {
        UserEventLog::trackRequest($request, $response);
    }
}
