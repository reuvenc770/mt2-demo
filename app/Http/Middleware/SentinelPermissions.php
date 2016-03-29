<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/8/16
 * Time: 12:23 PM
 */

namespace App\Http\Middleware;
use App\Facades\UserEventLog;
use Sentinel;
use Laracasts\Flash\Flash;
use Closure;
class SentinelPermissions
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
        if (!Sentinel::hasAccess($action))
        {
            UserEventLog::insertCustomRequest(Sentinel::getUser()->id,str_replace(".","/",$action),$request->getMethod(),\App\Models\UserEventLog::UNAUTHORIZED);
            if ($request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                Flash::warning('Sorry your account does not have permission to perform this action, please contact your manager.');
                return redirect()->back();
            }
        }
        if ($action != "myprofile" && !$request->wantsJson()) {
            if (!Sentinel::getUser()->email) {
                Flash::warning("Please enter an email before using the site");
                return redirect("myprofile");
            }
        }

        return $next($request);
    }
}
