<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

use App\Services\EspApiService;
use App\Repositories\EspApiRepo;

class EspApiProvider extends ServiceProvider
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
        App::bind( 'EspApi' , function () {
           return new EspApiService( new EspApiRepo( new Esp() ) ); 
        } );
    }
}
