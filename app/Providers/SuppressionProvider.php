<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 4/12/16
 * Time: 4:31 PM
 */

namespace App\Providers;

use App\Models\Suppression;
use App\Repositories\SuppressionRepo;
use App\Services\SuppressionService;
use Illuminate\Support\ServiceProvider;


class SuppressionProvider extends ServiceProvider
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
        $this->app->singleton('Suppression', function()
        {
            return new SuppressionService(new SuppressionRepo( new Suppression()));
        });
    }
}