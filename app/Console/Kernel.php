<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\GrabApiEspReports::class,
        Commands\GrabCsvEspReports::class,
        Commands\GrabTrackingApiData::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $filePath = storage_path('logs')."/downloadAPI.log";
        $schedule->command('reports:downloadApi BlueHornet 1')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Campaigner 1')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi EmailDirect 1')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Maro 1')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadTrackingData Cake 1')->hourly()->sendOutputTo($filePath);


    }
}
