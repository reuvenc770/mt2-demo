<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/24/17
 * Time: 11:05 AM
 */

namespace App\Providers;
use App;
use Illuminate\Support\ServiceProvider;

class BrontoMappingProvider extends ServiceProvider
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
        $this->app->singleton('BrontoMapping', function()
        {
            return new App\Services\BrontoIdMappingService(new App\Repositories\BrontoIdMappingRepo(new App\Models\BrontoIdMapping()));
        });

    }
}