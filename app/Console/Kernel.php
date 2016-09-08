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
    const REPORT_TIME_2 = '11:10';
    const EARLY_DELIVERABLE_SCHEDULE_TIME = '00:15';
    const EXPIRATION_RUNS = "01:15";
    const DEPLOY_CHECK_TIME = '14:00';
    const ATTRIBUTION_UPDATE_TIME = '15:30';
    const MT1_SYNC_TIME = '23:00';

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
        Commands\InsertChunksUtil::class,
        Commands\CheckDeployStats::class,
        Commands\RerunDeployStats::class,
        Commands\ExportActionsElsewhere::class,
        Commands\SendSuppressionsToMT1::class,
        Commands\FtpAdmin::class,
        Commands\Generator\EspServiceCommand::class,
        Commands\Generator\EspApiCommand::class,
        Commands\Generator\EspGeneration::class,
        Commands\Generator\EspModelCommand::class,
        Commands\Generator\EspSeedCommand::class,
        Commands\FilterJobQueue::class,
        Commands\RunScheduledFilter::class,
        Commands\CommitAttribution::class,
        Commands\SharePublicatorsUnsubs::class,
        Commands\UpdateAttributionReports::class,
        Commands\PopulateCfsStatsTables::class,
        Commands\PopulateAttributionRecordReport::class,
        Commands\ImportMt1Entity::class,
        Commands\AttributionBatchProcess::class,
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
        $schedule->command('suppression:downloadESP BlueHornet 1')->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP Maro 1')->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP Campaigner 1')->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP EmailDirect 1')->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP Publicators 1')->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP Bronto 1')->dailyAt(self::UNSUB_TIME);
        
        $schedule->command('exportUnsubs BhSuppressionReport --lookback=1')->dailyAt(self::REPORT_TIME);
        $schedule->command('exportUnsubs CampaignerSuppressionReport --lookback=1')->dailyAt(self::REPORT_TIME_2);
        $schedule->command('exportUnsubs emailsForOpensClicks --lookback=15')->dailyAt(self::REPORT_TIME);
        $schedule->command('exportUnsubs ZxSprintUnsubExport --lookback=1')->dailyAt(self::REPORT_TIME);
        $schedule->command('exportUnsubs ZxEsuranceUnsubExport --lookback=1')->dailyAt(self::REPORT_TIME);

        $schedule->command( 'suppression:sendToMT1 3' )->dailyAt( self::REPORT_TIME )->sendOutputTo( $unsubFilePath );
        $schedule->command('suppression:exportPublicators 1')->cron('10 */4 * * *');
        
        /**
         * Campaign Data Daily
         */
        $filePath = storage_path('logs')."/downloadAPI.log";
        $schedule->command('reports:downloadApi BlueHornet 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Campaigner 5')->hourly()->sendOutputTo($filePath);
        #$schedule->command('reports:downloadApi EmailDirect 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Maro 5')->hourly()->sendOutputTo($filePath);
        //$schedule->command('reports:downloadApi Ymlp 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Publicators 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Bronto 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadTrackingData Cake 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('process:cfsStats')->cron('0 */4 * * *');

        /**
         * Campaign Data Monthly
         */
        $schedule->command('reports:downloadApi BlueHornet 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Campaigner 31')->monthly()->sendOutputTo($filePath);
        #$schedule->command('reports:downloadApi EmailDirect 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Maro 31')->monthly()->sendOutputTo($filePath);
        //$schedule->command('reports:downloadApi Ymlp 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Publicators 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Bronto 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadTrackingData Cake 31')->monthly()->sendOutputTo($filePath);

        /**
         * Deliverable Data
         */
        $deliverableFilePath = storage_path( 'logs' ) . "/downloadDeliverables.log";
        $schedule->command( 'reports:downloadDeliverables BlueHornet 5 BlueHornet' )->dailyAt( self::EARLY_DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Campaigner 5 Campaigner' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        #$schedule->command( 'reports:downloadDeliverables EmailDirect 5' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Maro 2' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Maro:delivered 2' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        //$schedule->command( 'reports:downloadDeliverables Ymlp 5' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Publicators 5 Publicators' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Bronto 2' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        //$schedule->command( 'reports:downloadDeliverables Bronto:delivered 2' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:populateStats')->dailyAt(self::DELIVERABLE_AGGREGATION_TIME)->sendOutputTo($deliverableFilePath);
        //$schedule->command( 'reports:populateAttrBaseRecords')->dailyAt(self::DELIVERABLE_AGGREGATION_TIME)->sendOutputTo($deliverableFilePath);
        $schedule->command('process:useragents')->dailyAt(self::DELIVERABLE_AGGREGATION_TIME);
        $schedule->command('download:mtstats')->dailyAt(self::DELIVERABLE_SCHEDULE_TIME);
        $schedule->command('reports:findIncompleteDeploys')->dailyAt(self::DEPLOY_CHECK_TIME);
        

        /**
         * Constantly firing.
         *
         */
        $schedule->command('ftp:admin -H 52.205.67.250 -U root -k ~/.ssh/mt2ftp.pub -K ~/.ssh/mt2ftp -u -s Client')->everyFiveMinutes();

        /**
         *  MT1 data sync jobs
         */
        $schedule->command('mt1Import offer')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import listProfile')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import advertiser')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('emails:download')->cron('*/2 * * * * *')->withoutOverlapping();
        $schedule->command('mt1Import creative')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import from')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import subject')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import deploy')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import offerCreativeMap')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import offerFromMap')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import offerSubjectMap')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import feed')->cron('0 * * * * *');

        /**
         * Attribution Jobs
         */
        $schedule->command('runFilter activity')->dailyAt(self::EXPIRATION_RUNS);
        $schedule->command('runFilter expiration')->dailyAt(self::EXPIRATION_RUNS);
        $schedule->command('attribution:commit')->dailyAt(self::ATTRIBUTION_UPDATE_TIME);
        //$schedule->command('attribution:updateReports Client')->dailyAt(self::ATTRIBUTION_UPDATE_TIME);
    }
}
