<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/8/16
 * Time: 12:23 PM
 */

namespace App\Http\Middleware;
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
            if ($request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                Flash::warning('Sorry your account does not have permission to preform this action, please contact your manager.');
                return redirect()->back();
            }
        }

        return $next($request);
    }
}