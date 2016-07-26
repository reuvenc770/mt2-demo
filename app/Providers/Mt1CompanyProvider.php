<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\MT1Models\CompanyInfo;
use App\Repositories\MT1Repositories\CompanyInfoRepo;
use App\Services\MT1Services\CompanyService;


class Mt1CompanyProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->app->bind('App\Services\MT1Services\CompanyService', function() {
            return new CompanyService(new CompanyInfoRepo( new CompanyInfo()));
        });
    }
}