<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    const DELIVERABLE_SCHEDULE_TIME = '08:00';

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
        Commands\UpdatePermissionsFromRoutes::class,
        Commands\GrabDeliverableReports::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * Campaign Data
         */
        $filePath = storage_path('logs')."/downloadAPI.log";
        $schedule->command('reports:downloadApi BlueHornet 1')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Campaigner 1')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi EmailDirect 1')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Maro 1')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Aweber 1')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Ymlp 1')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi GetResponse 1')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadTrackingData Cake 1')->hourly()->sendOutputTo($filePath);

        /**
         * Deliverable Data
         */
        $deliverableFilePath = storage_path( 'logs' ) . "/downloadDeliverables.log";
        $schedule->command( 'reports:downloadDeliverables BlueHornet 1' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Campaigner 1' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables EmailDirect 1' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Maro 1' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables AWeber 1' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Ymlp 1' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
    }
}
