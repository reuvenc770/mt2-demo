<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Factories\APIFactory;

class AWeberReportServiceProvider extends ServiceProvider
{
    const ESP_NAME = 'AWeber'; 

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
        $this->app->when('App\Http\Controllers\AWeberDeployMappingController')
            ->needs('App\Services\AWeberReportService')
            ->give(function () {
                $espAccount = $this->espRepo->getAccountsByESPName( self::ESP_NAME )->first();

                return APIFactory::createApiReportService( self::ESP_NAME , $espAccount->id );
            });

    }
}
