<?php
namespace App\Http\Middleware;
use Closure;
use Laracasts\Flash\Flash;
use Sentinel;
class SentinelDevUser
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
        $user = Sentinel::getUser();
        $dev = Sentinel::findRoleBySlug('gtdev');
        if (!$user->inRole($dev)) {
            Flash::warning("You do not have permission to reach this page");
            return redirect('/home');
        }
        return $next($request);
    }
}