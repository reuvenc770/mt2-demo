<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    const DELIVERABLE_SCHEDULE_TIME = '02:00';
    const DELIVERABLE_AGGREGATION_TIME = '11:00';
    const UNSUB_TIME = '01:00';
    const REPORT_TIME = '11:30';
    const EARLY_DELIVERABLE_SCHEDULE_TIME = '00:15';
    const DEPLOY_CHECK_TIME = '14:00';

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
        Commands\DownloadContentServerStats::class,
        Commands\ProcessUserAgents::class,
        Commands\SendSprintUnsubsCommand::class,
        Commands\DownloadSuppressionFromESPCommand::class,
        Commands\ParseandSendSuppressionsCommand::class,
        Commands\InsertChunksUtil::class,
        Commands\CheckDeployStats::class,
        Commands\SendSuppressionsToMT1::class,
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
         * Unsub Jobs
         */
        $unsubFilePath = storage_path( 'logs' ) . "/unsubJobs.log";
        $schedule->command( 'ftp:sendSprintUnsubs --ftpCleanup=1' )->dailyAt( '10:00' )->sendOutputTo( $unsubFilePath );
        $schedule->command( 'ftp:sendSprintUnsubs' )->dailyAt( '13:00' )->sendOutputTo( $unsubFilePath );
        $schedule->command( 'ftp:sendSprintUnsubs' )->dailyAt( '17:00' )->sendOutputTo( $unsubFilePath );


        /**
         * Orphan Adoption
         */
        $orphanFilePath = storage_path('logs')."/adoptOrphans.log";
        #$schedule->command( 'reports:adoptOrphans --maxOrphans=400000 --chunkSize=10000 --queueName=orphanage --chunkDelay=0 --order=newest --maxAttempts=2' )->everyTenMinutes()->sendOutputTo( $orphanFilePath );
        #$schedule->command( 'reports:adoptOrphans --maxOrphans=400000 --chunkSize=10000 --queueName=orphanage --chunkDelay=0 --order=oldest --maxAttempts=2' )->everyTenMinutes()->sendOutputTo( $orphanFilePath );


        /**
         * Suppression Jobs
         */
        $schedule->command('suppression:downloadESP BlueHornet 1')->hourly()->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP Maro 1')->hourly()->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP Campaigner 1')->hourly()->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP EmailDirect 1')->hourly()->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP Publicators 1')->hourly()->dailyAt(self::UNSUB_TIME);
        //$schedule->command('suppression:downloadESP YMLP 1')->hourly()->dailyAt(self::UNSUB_TIME);
        
        $schedule->command('movetoftp:suppressions BlueHornet 1')->hourly()->dailyAt(self::REPORT_TIME);

        /**
         * Campaign Data Daily
         */
        $filePath = storage_path('logs')."/downloadAPI.log";
        $schedule->command('reports:downloadApi BlueHornet 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Campaigner 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi EmailDirect 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Maro 5')->hourly()->sendOutputTo($filePath);
        //$schedule->command('reports:downloadApi Ymlp 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Publicators 5')->hourly()->sendOutputTo($filePath);
        //$schedule->command('reports:downloadApi Bronto 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadTrackingData Cake 5')->hourly()->sendOutputTo($filePath);

        /**
         * Campaign Data Monthly
         */
        $schedule->command('reports:downloadApi BlueHornet 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Campaigner 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi EmailDirect 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Maro 31')->monthly()->sendOutputTo($filePath);
        //$schedule->command('reports:downloadApi Ymlp 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Publicators 31')->monthly()->sendOutputTo($filePath);
        //$schedule->command('reports:downloadApi Bronto 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadTrackingData Cake 31')->monthly()->sendOutputTo($filePath);

        /**
         * Deliverable Data
         */
        $deliverableFilePath = storage_path( 'logs' ) . "/downloadDeliverables.log";
        $schedule->command( 'reports:downloadDeliverables BlueHornet 5 BlueHornet' )->dailyAt( self::EARLY_DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Campaigner 5 Campaigner' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables EmailDirect 5' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Maro 2' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Maro:delivered 2' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        //$schedule->command( 'reports:downloadDeliverables Ymlp 5' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Publicators 5 Publicators' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        //$schedule->command( 'reports:downloadDeliverables Bronto 2' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        //$schedule->command( 'reports:downloadDeliverables Bronto:delivered 2' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        //$schedule->command( 'reports:populateStats')->dailyAt(self::DELIVERABLE_AGGREGATION_TIME)->sendOutputTo($deliverableFilePath);
        $schedule->command('emails:download')->cron('*/2 * * * * *')->withoutOverlapping();
        $schedule->command('process:useragents')->dailyAt(self::DELIVERABLE_AGGREGATION_TIME);
        $schedule->command('download:mtstats')->dailyAt(self::DELIVERABLE_SCHEDULE_TIME);
        $schedule->command('reports:findIncompleteDeploys')->dailyAt(self::DEPLOY_CHECK_TIME);
    }
}
