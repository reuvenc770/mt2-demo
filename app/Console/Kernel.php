<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Predis\Command\Command;

class Kernel extends ConsoleKernel
{
    const DELIVERABLE_SCHEDULE_TIME = '02:00';
    const DELIVERABLE_AGGREGATION_TIME = '11:00';
    const UNSUB_TIME = '01:00';
    const REPORT_TIME = '11:30';
    const REPORT_TIME_2 = '11:10';
    const EARLY_DELIVERABLE_SCHEDULE_TIME = '00:15';
    const EXPIRATION_RUNS = "01:15";
    const DROP_OFF_LIST_PROFILES = "05:00";
    const DEPLOY_CHECK_TIME = '14:00';
    const UPDATE_SOURCE_COUNTS = '14:00';
    const CAKE_CONVERSION_UPDATE_TIME = '14:00';
    const ATTRIBUTION_UPDATE_TIME = '15:30';
    const ATTRIBUTION_REPORT_EARLY_UPDATE_TIME = '0:30';
    const ATTRIBUTION_REPORT_UPDATE_TIME = '17:00';
    const FEED_FILE_PROCESS_TIME = '22:00';
    const MT1_SYNC_TIME = '23:00';
    const REDSHIFT_UPLOAD_TIME = '09:00';
    const AWEBER_TIME = '19:00';
    const MIDNIGHT = '00:00';

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\GrabApiEspReports::class,
        Commands\GrabTrackingApiData::class,
        Commands\UpdatePermissionsFromRoutes::class,
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
        Commands\SendDeploysToOps::class,
        Commands\SyncMT1FeedLevels::class,
        Commands\AttributionReportCommand::class,
        Commands\PopulateListProfileAggregationTable::class,
        Commands\NotifySomeoneAboutSomething::class,
        Commands\PullContentServerRecordData::class,
        Commands\InflateEmailHistoriesUtil::class,
        Commands\BuildBaseListProfileTables::class,
        Commands\ExportListProfile::class,
        Commands\ESPUnsubsReport::class,
        Commands\ProcessFeedRecords::class,
        Commands\DeactivateEspAccounts::class,
        Commands\ProcessFeedRawFiles::class,
        Commands\UpdateActionStatus::class,
        Commands\ExportThirdPartyData::class,
        Commands\SuppressFeed::class,
        Commands\PassToMt1::class,
        Commands\UpdateFeedCounts::class,
        Commands\S3RedshiftExport::class,
        Commands\FindMissingStatsForAWeber::class,
        Commands\UpdateMissingMaroCampaigns::class,
        Commands\UpdateAWeberLists::class,
        Commands\GrabAWeberSubscribers::class,
        Commands\ProcessAWeberActions::class,
        Commands\UpdateMissingCampaignerCampaigns::class,
        Commands\VacuumRedshift::class,
        Commands\CleanUpRawContentServerActions::class,
        Commands\SumBrontoStandardReports::class,
        Commands\DomainExpirationNotification::class,
        Commands\ProcessNewActionsCommand::class,
        Commands\DataConsistencyValidation::class,
        Commands\RedshiftDataConsistencyValidation::class,
        Commands\AttributionFeasibilityValidation::class,
        Commands\RegenerateAttributionModelReportTables::class,
        Commands\TestFeedFileGenerator::class,
        Commands\BulkInsertDelivers::class,
        Commands\SyncModelsWithNewFeedsCommand::class,
        Commands\ResetUserPasswordCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        #$schedule->command('domains:expired')->dailyAt(self::REPORT_TIME);

        /**
         * Orphan Adoption
         */
        $orphanFilePath = storage_path('logs')."/adoptOrphans.log";
        #$schedule->command( 'reports:adoptOrphans --maxOrphans=400000 --chunkSize=10000 --queueName=orphanage --chunkDelay=0 --order=newest --maxAttempts=2' )->everyTenMinutes()->sendOutputTo( $orphanFilePath );
        #$schedule->command( 'reports:adoptOrphans --maxOrphans=400000 --chunkSize=10000 --queueName=orphanage --chunkDelay=0 --order=oldest --maxAttempts=2' )->everyTenMinutes()->sendOutputTo( $orphanFilePath );


        /**
         * Suppression Jobs
         */
        $schedule->command('suppression:downloadESP BlueHornet 5')->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP Maro 5')->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP Campaigner 5')->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP EmailDirect 5')->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP Publicators 5')->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP Bronto 5')->dailyAt(self::UNSUB_TIME);
        $schedule->command('suppression:downloadESP AWeber 5')->dailyAt(self::UNSUB_TIME);

        $schedule->command('reports:generateEspUnsubReport --lookback=1')->dailyAt(self::REPORT_TIME);
        $schedule->command('exportUnsubs emailsForOpensClicks --lookback=15')->dailyAt(self::REPORT_TIME);
        $schedule->command('exportUnsubs ZxSprintUnsubExport --lookback=1')->dailyAt(self::REPORT_TIME);
        $schedule->command('exportUnsubs ZxEsuranceUnsubExport --lookback=1')->dailyAt(self::REPORT_TIME);

        $unsubFilePath = storage_path( 'logs' ) . "/unsubJobs.log";
        $schedule->command( 'suppression:sendToMT1 3' )->dailyAt( self::REPORT_TIME )->sendOutputTo( $unsubFilePath ); //FTPSuppressionsToMT1
        $schedule->command('suppression:exportPublicators 1')->cron('10 */4 * * *');
        
        /**
         * Campaign Data Daily
         */
        $filePath = storage_path('logs')."/downloadAPI.log";
        $schedule->command('reports:downloadApi BlueHornet --daysBack=5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Campaigner --daysBack=5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi AWeber --daysBack=5 --apiLimit=40')->cron("0 0,6,12,18 * * *")->sendOutputTo($filePath);
        #$schedule->command('reports:downloadApi EmailDirect --daysBack=5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Maro --daysBack=5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:updateMissingMaroCampaigns')->daily()->sendOutputTo($filePath);
        //$schedule->command('reports:downloadApi Ymlp --daysBack=5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Publicators --daysBack=5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Bronto --daysBack=5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadTrackingData Cake 5')->hourly()->sendOutputTo($filePath);
        $schedule->command('process:cfsStats')->cron('0 */4 * * *'); // Job name like: ProcessCfsStats
        
        

        /**
         * Campaign Data Monthly
         */
        $schedule->command('reports:downloadApi BlueHornet --daysBack=31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Campaigner --daysBack=31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi AWeber --daysBack=31 --apiLimit=200')->monthly()->sendOutputTo($filePath);
        #$schedule->command('reports:downloadApi EmailDirect --daysBack=31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Maro --daysBack=31')->monthly()->sendOutputTo($filePath);
        //$schedule->command('reports:downloadApi Ymlp --daysBack=31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Publicators --daysBack=31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Bronto --daysBack=31')->monthly()->sendOutputTo($filePath);
        #$schedule->command('reports:downloadTrackingData Cake 31')->monthly()->sendOutputTo($filePath);
        $schedule->command('aweber:processUniques 31')->monthly()->sendOutputTo($filePath);

        /**
         * Record-level Data
         * Job name like: RetrieveDeliverableReports%
         */
        $deliverableFilePath = storage_path( 'logs' ) . "/downloadDeliverables.log";
        $schedule->command( 'reports:downloadDeliverables Campaigner:delivered 2 Campaigner' )->dailyAt( self::EARLY_DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Campaigner:actions 5 Campaigner' )->cron('0 0,10 * * * *');
        $schedule->command( 'reports:downloadDeliverables BlueHornet:delivered 3 BlueHornet' )->dailyAt( self::EARLY_DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command('reports:downloadDeliverables BlueHornet:actions 5 BlueHornet')->cron('0 0,10 * * * *');
        #$schedule->command( 'reports:downloadDeliverables EmailDirect 5' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Maro 2 Maro' )->cron('0 0,10 * * * *')->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Maro:delivered 2 Maro' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        //$schedule->command( 'reports:downloadDeliverables Ymlp 5' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Bronto:actions 5 Bronto' )->cron('0 0,10 * * * *');
        $schedule->command( 'reports:downloadDeliverables Bronto:delivered 2 Bronto' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Publicators:actions 5 Publicators' )->cron('0 0,10 * * * *');
        $schedule->command( 'reports:downloadDeliverables Publicators:delivers 2 Publicators' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables AWeber 5 AWeber' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );

        $schedule->command( 'reports:populateStats')->dailyAt(self::DELIVERABLE_AGGREGATION_TIME)->sendOutputTo($deliverableFilePath); // Job name like: PopulateEmailCampaignStats, PullCakeDeliverableStats
        //$schedule->command( 'reports:populateAttrBaseRecords')->dailyAt(self::DELIVERABLE_AGGREGATION_TIME)->sendOutputTo($deliverableFilePath);
        $schedule->command('process:useragents')->hourly(); // Job name like: ProcessUserAgents
        $schedule->command('reports:findIncompleteDeploys')->dailyAt(self::DEPLOY_CHECK_TIME);
        $schedule->command('insert:delivers 2')->cron('0 6 * * * *'); // Job name like: BulkInsertDelivers


        /**
         *  Deactivation jobs
         */
        $schedule->command('deactivate:espAccounts')->dailyAt(self::MIDNIGHT);


        /**
         * Constantly firing.
         *
         */
        $schedule->command('ftp:admin -H ' . config('ssh.servers.mt1_feed_file_server.host') . ' -U ' . config('ssh.servers.mt1_feed_file_server.username') . ' -k ' . config('ssh.servers.mt1_feed_file_server.public_key') . ' -K ' . config('ssh.servers.mt1_feed_file_server.private_key') . ' -u -s Feed')->everyFiveMinutes();
        $schedule->command( 'attribution:syncModelsWithNewFeeds' )->everyThirtyMinutes();

        /**
         *  MT1 data sync jobs
         *  Job names like: ImportMt1
         */
        $schedule->command('mt1Import offer')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import advertiser')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('emails:download 0')->cron('*/2 * * * * *');
        $schedule->command('emails:download 1')->cron('*/2 * * * * *');
        $schedule->command('emails:download 2')->cron('*/2 * * * * *');
        $schedule->command('emails:download 3')->cron('*/2 * * * * *');
        $schedule->command('emails:download 4')->cron('*/2 * * * * *');
        $schedule->command('mt1Import creative')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import from')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import subject')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import deploy')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import offerCreativeMap')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import offerFromMap')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import offerSubjectMap')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import cakeEncryptedLinkMap')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import link 2')->cron('0 */2 * * * *');
        $schedule->command('mt1Import feed')->cron('0 * * * * *');
        $schedule->command('mt1Import offerTrackingLink')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import mailingTemplate')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import cakeOffer')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import cakeVertical')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import cakeOfferMap')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import client')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import vendorSuppressionInfo')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import offerSuppressionListMap')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import globalSuppression')->dailyAt(self::MT1_SYNC_TIME);

        /**
         * Attribution Jobs
         */
         
        // Attribution jobs disabled temporarily until launch
        $schedule->command('runFilter expiration')->dailyAt(self::EXPIRATION_RUNS); // Job name: Scheduled Filter Expiration
        #$schedule->command('attribution:commit daily')->dailyAt(self::ATTRIBUTION_UPDATE_TIME);
        $schedule->command( 'attribution:conversion -P realtime' )->dailyAt( self::ATTRIBUTION_REPORT_EARLY_UPDATE_TIME ); #early conversion grab & report updating
        $schedule->command( 'attribution:conversion -P rerun' )->dailyAt( self::ATTRIBUTION_REPORT_UPDATE_TIME ); #daily rerun
        $schedule->command( 'attribution:conversion -P rerun -d 7' )->weekly(); #weekly rerun
        $schedule->command( 'attribution:conversion -P rerun -D month -m current' )->monthlyOn( 20 , self::ATTRIBUTION_REPORT_UPDATE_TIME ); #early monthly rerun
        $schedule->command( 'attribution:conversion -P rerun -D month -m current' )->monthlyOn( 28 , self::ATTRIBUTION_REPORT_UPDATE_TIME ); #monthly rerun
        $schedule->command( 'attribution:conversion -P rerun -D month -m last' )->monthlyOn( 1 , self::ATTRIBUTION_REPORT_UPDATE_TIME ); #final monthly rerun
        $schedule->command('attribution:validate')->dailyAt(self::FEED_FILE_PROCESS_TIME); // Job name: AttributionValidation
        
        /**
         *  List profile jobs
         */

        $schedule->command('listprofile:dataEtl')->cron('30 6,13,16 * * 1-6 *'); // Job names like: %-s3
        $schedule->command('listprofile:dataEtl --all')->cron('0 1 * * 7 *');
        $schedule->command('listprofile:optimize')->weekly();
        $schedule->command('listprofile:aggregateActions')->cron('0 4,14 * * * *'); // Job name: ListProfileAggregation
        $schedule->command('listprofile:contentServerRawStats')->hourly(); // Job name: ProcessContentServerRawStats
        $schedule->command('listprofile:getRecordAgentData 2')->hourly(); // Job name: ContentServerDeviceData
        $schedule->command('listprofile:baseTables')->cron('30 7,12,16 * * 1-6 *'); // Job name like: ListProfileExport%
        $schedule->command('listprofile:validateRedshift 1')->cron('0 4 * * * *'); // Job names like: DataValidation & upper-case entity

        /**
         * Feed File Processing
         */
        $schedule->command( 'feedRecords:processRawFiles' )->everyFiveMinutes(); // Job name like: ProcessFeedRawFilesJob%
        $schedule->command( 'feedRecords:updateCounts' )->dailyAt( self::EARLY_DELIVERABLE_SCHEDULE_TIME ); // Job name: UpdateFeedCountJob
        $schedule->command( 'feedRecords:updateCounts' )->dailyAt( self::UPDATE_SOURCE_COUNTS );

        // Currently commented-out. Waiting for everything going live
        // Process first party feeds, by feed id
        #$schedule->command('feedRecords:process 1 --feed=2983')->cron('*/2 * * * * *');
        #$schedule->command('feedRecords:process 1 --feed=2971')->cron('*/2 * * * * *');
        #$schedule->command('feedRecords:process 1 --feed=2972')->cron('*/2 * * * * *');
        #$schedule->command('feedRecords:process 1 --feed=2987')->cron('*/2 * * * * *');
        #$schedule->command('feedRecords:process 1 --feed=2759')->cron('*/2 * * * * *');
        #$schedule->command('feedRecords:process 1 --feed=2798')->cron('*/2 * * * * *');
        #$schedule->command('feedRecords:process 1 --feed=2979')->cron('*/2 * * * * *');
        
        // Process third party feeds, broken down by starting letter of email address
        $schedule->command('feedRecords:process 3 --startChars=0123456789')->cron('*/2 * * * * *'); // Job names like: FeedProcessing%
        $schedule->command('feedRecords:process 3 --startChars=ab')->cron('*/2 * * * * *');
        $schedule->command('feedRecords:process 3 --startChars=cd')->cron('*/2 * * * * *');
        $schedule->command('feedRecords:process 3 --startChars=efgh')->cron('*/2 * * * * *');
        $schedule->command('feedRecords:process 3 --startChars=ij')->cron('*/2 * * * * *');
        $schedule->command('feedRecords:process 3 --startChars=lk')->cron('*/2 * * * * *');
        $schedule->command('feedRecords:process 3 --startChars=mno')->cron('*/2 * * * * *');
        $schedule->command('feedRecords:process 3 --startChars=pqrs')->cron('*/2 * * * * *');
        $schedule->command('feedRecords:process 3 --startChars=tuvwxyz')->cron('*/2 * * * * *');
        
        // Export some third party feeds to external sources
        #$schedule->command('feedRecords:exportThirdParty 2430')->cron('*/2 * * * * *');
        #$schedule->command('feedRecords:exportThirdParty 2433')->cron('*/2 * * * * *');
        #$schedule->command('feedRecords:exportThirdParty 2957')->cron('*/2 * * * * *');
        
        // Re-run first party actives against suppression
        #$schedule->command('feedRecords:reprocessFirstParty 1')->dailyAt(self::DELIVERABLE_AGGREGATION_TIME);

        /**
         * AWeber Jobs
         */
        $schedule->command('aweber:processUniques 15')->cron("10 0,6,12,18 * * *")->sendOutputTo($filePath); // Job name like: ProcessAweberUniques
        $schedule->command('aweber:updateAWeberLists' )->dailyAt( self::AWEBER_TIME); // Job name: AWeberUpdateLists
        $schedule->command('aweber:processAWeberActions')->cron("30 0,6,12,18 * * *")->sendOutputTo($filePath); // Job name: AWeberActionImmigration


        /**
         * Bronto Jobs
         */

        $schedule->command("reports:sumBronto")->cron("15 * * * *");

        /**
         *  Data consistency jobs
         *  Job names like DataValidation followed by lower case entity (middle item)
         */
        $schedule->command("dataValidation emails exists")->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command("dataValidation emailFeedInstances exists")->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command("dataValidation emailFeedAssignments value")->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command("newActions:process --hoursBack=2")->cron("30 * * * * *"); // Job name like: ProcessNewActions%
    }
}
