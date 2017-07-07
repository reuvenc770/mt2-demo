<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Providers;

use App\Services\NotificationScheduleService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class NotificationScheduleServiceProvider extends ServiceProvider
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

        App::bind('notificationSchedule', function()
        {
            return App::make( NotificationScheduleService::class );
        });

    }
}
