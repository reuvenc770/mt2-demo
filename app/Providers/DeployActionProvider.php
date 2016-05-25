<?php

namespace App\Providers;

use App\Models\DeployActionEntry;
use App\Repositories\DeployActionRepo;
use App\Services\DeployActionService;
use Illuminate\Support\ServiceProvider;

class DeployActionProvider extends ServiceProvider
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
        $this->app->singleton('DeployActionEntry', function()
        {
            return new DeployActionService(new DeployActionRepo(new DeployActionEntry()));
        });
    }
}
