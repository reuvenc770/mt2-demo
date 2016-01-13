<?php

namespace App\Providers;

use App\Models\Report;
use App\Repositories\ReportsRepo;
use App\Services\BlueHornetService;
use Illuminate\Support\ServiceProvider;
class BlueHornetServiceProvider extends ServiceProvider
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

    //This is more for the test then the real implentation, i am expecting us to pass api creds to the jobs itself
    //at a later time.
    public function register()
    {
        $this->app->bind('App\Services\BlueHornetService', function()
        {
            $report = new Report();
            $reportRepo = new ReportsRepo($report);
            return new BlueHornetService($reportRepo,'ced21d9cfb0655eccf3946585d6b0fde','bdc925fe6cbd7596dc2a5e71bc211caa');
        });
    }
}
