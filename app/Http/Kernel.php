<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
        \App\Http\Middleware\UserEventLogTrack::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    //will add back middlewaregroups once more of the app is fleshed out.
    protected $middlewareGroups = [

    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'         => \App\Http\Middleware\SentinelAuthenticate::class,
        'guest'        => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'pageLevel'    => \App\Http\Middleware\SentinelPermissions::class,
        'dev'          => \App\Http\Middleware\SentinelDevUser::class,

    ];
}
