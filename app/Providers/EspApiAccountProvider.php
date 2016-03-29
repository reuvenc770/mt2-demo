<?php

namespace App\Providers;
use App\Repositories\EspApiAccountRepo;
use App\Services\EspApiAccountService;
use Illuminate\Support\Facades\App;

use App\Models\EspAccount;
use Illuminate\Support\ServiceProvider;

class EspApiAccountProvider extends ServiceProvider
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
        App::bind('EspApiAccount', function()
        {
            return new EspApiAccountService(new EspApiAccountRepo(new EspAccount()));
        });

    }
}
