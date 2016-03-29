<?php

namespace App\Providers;
use App\Models\Permission;

use App\Repositories\PermissionRepo;
use App\Services\PermissionService;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
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
     */
    public function register()
    {
        $this->app->singleton('Permission', function()
        {
            return new PermissionService(new PermissionRepo(new Permission()));
        });

    }
}
