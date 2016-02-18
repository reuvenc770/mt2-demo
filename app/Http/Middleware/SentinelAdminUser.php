<?php
namespace App\Http\Middleware;
use Closure;
use Laracasts\Flash\Flash;
use Sentinel;
class SentinelAdminUser
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
        $admin = Sentinel::findRoleByName('Admin');
        if (!$user->inRole($admin)) {
            Flash::warning("Your account does not have the proper role to reach this page");
            return redirect('/home');
        }
        return $next($request);
    }
}