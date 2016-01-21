<?php

namespace App\Providers;
use App\Repositories\EspAccountRepo;
use App\Services\ESPAccountService;
use Illuminate\Support\Facades\App;

use App\Models\EspAccount;
use Illuminate\Support\ServiceProvider;

class ESPAccountProvider extends ServiceProvider
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
        App::bind('espAccount', function()
        {
            return new ESPAccountService(new EspAccountRepo(new EspAccount()));
        });

    }
}
