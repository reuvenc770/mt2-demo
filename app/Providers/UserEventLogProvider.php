<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Sentinel;
use App\Services\UserEventLogService;
use App\Repositories\UserEventLogRepo;
use App\Models\UserEventLog;
class UserEventLogProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('UserEventLog', function()
        {
            return new UserEventLogService(new UserEventLogRepo(new UserEventLog()));
        });
    }
}
