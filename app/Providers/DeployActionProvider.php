<?php

namespace App\Providers;

use App\Models\DeployActionEntry;
use App\Models\StandardReport;
use App\Repositories\DeployActionRepo;
use App\Repositories\StandardApiReportRepo;
use App\Services\DeployActionService;
use App\Services\StandardReportService;
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
        $this->app->singleton('DeployActionEntry', function () {
            return new DeployActionService(new DeployActionRepo(new DeployActionEntry()));
        });
        $this->app->when('App\Http\Controllers\AWeberDeployMappingController')
            ->needs('App\Services\StandardReportService')
            ->give(function () {
                return new StandardReportService(new StandardApiReportRepo(new StandardReport()));
            });
    }
}
