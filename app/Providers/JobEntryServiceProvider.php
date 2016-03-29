<?php

namespace App\Providers;

use App\Models\JobEntry;
use App\Repositories\JobEntryRepo;
use App\Services\JobEntryService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class JobEntryServiceProvider extends ServiceProvider
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

        App::bind('jobEntry', function()
        {
            return new JobEntryService(new JobEntryRepo(new JobEntry()));
        });

    }
}
