<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    const DELIVERABLE_SCHEDULE_TIME = '02:00';
    const DELIVERABLE_AGGREGATION_TIME = '11:00';

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
        Commands\GrabCsvDeliverables::class,
        Commands\GrabDeliverableReports::class,
        Commands\PopulateEmailCampaignsTable::class,
        Commands\GenOauth::class,
        Commands\ImportMt1Emails::class,
        Commands\AdoptOrphanEmails::class,
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
         * Orphan Adoption
         */
        $orphanFilePath = storage_path('logs')."/adoptOrphans.log";
        $schedule->command( 'reports:adoptOrphans --maxOrphans=100000 --chunkSize=10000 --chunkDelay=120' )->hourly()->sendOutputTo( $orphanFilePath );

        /**
         * Campaign Data Daily
         */
        $filePath = storage_path('logs')."/downloadAPI.log";
        $schedule->command('reports:downloadApi BlueHornet 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Campaigner 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi EmailDirect 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Maro 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Aweber 5 AWeber')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Ymlp 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi GetResponse 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadTrackingData Cake 5')->hourly()->sendOutputTo($filePath);

        /**
         * Campaign Data Monthly
         */
        $schedule->command('reports:downloadApi BlueHornet 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Campaigner 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi EmailDirect 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Maro 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Ymlp 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi GetResponse 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadTrackingData Cake 31')->monthly()->sendOutputTo($filePath);

        /**
         * Deliverable Data
         */
        $deliverableFilePath = storage_path( 'logs' ) . "/downloadDeliverables.log";
        $schedule->command( 'reports:downloadDeliverables BlueHornet 5' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Campaigner 5' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables EmailDirect 5' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Maro 5' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Maro:delivered 5' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Ymlp 1' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:populateStats')->dailyAt(self::DELIVERABLE_AGGREGATION_TIME)->sendOutputTo($deliverableFilePath);
        $schedule->command('emails:download')->cron('*/20 * * * * *')->withoutOverlapping();
    }
}
