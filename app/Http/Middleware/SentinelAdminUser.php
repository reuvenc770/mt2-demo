<?php
namespace App\Http\Middleware;
use Closure;
use Laracasts\Flash\Flash;
use Sentinel;
use App\Facades\UserEventLog;
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
        $action = $request->route()->getAction()['as'];
        $admin = Sentinel::findRoleByName('Admin');
        if (!$user->inRole($admin)) {
            UserEventLog::insertCustomRequest(Sentinel::getUser()->id,str_replace(".","/",$action),$request->getMethod(),\App\Models\UserEventLog::UNAUTHORIZED);
            Flash::warning("Your account does not have the proper role to reach this page");
            return redirect('/home');
        }
        return $next($request);
    }
}