<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/6/17
 * Time: 12:49 PM
 */

namespace app\Providers;
use App;
use Illuminate\Support\ServiceProvider;
use App\Services\AWeberEmailActionsService;
use App\Repositories\AWeberEmailActionsRepo;
use App\Models\AweberEmailActionsStorage;
class AWeberEmailActionProvider extends ServiceProvider
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
        $this->app->singleton('AWeberEmailAction', function()
        {
            return new AWeberEmailActionsService(new AWeberEmailActionsRepo(new AweberEmailActionsStorage()));
        });

    }
}