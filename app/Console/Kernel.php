<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Predis\Command\Command;

class Kernel extends ConsoleKernel
{
    const EARLY_DELIVERABLE_SCHEDULE_TIME = '20:00';
    const DELIVERABLE_SCHEDULE_TIME = '22:00';

    const DELIVERABLE_AGGREGATION_TIME = '11:00';
    const UNSUB_TIME = '01:00';
    const REPORT_TIME = '11:30';
    const REPORT_TIME_2 = '11:10';
    
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
        Commands\ProcessMt1BatchFeedFiles::class ,
        Commands\ProcessMt1RealtimeFeedFiles::class ,
        Commands\ProcessMt1FirstPartyFeedFiles::class ,
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
        Commands\SimpleTestCommand::class,
        Commands\RunTimeMonitorCommand::class,
        Commands\PopulateMappingTable::class,
        Commands\CompareMt1AndCmpExports::class,
        Commands\CheckMt1BatchFeedProcessingCommand::class,
        Commands\CheckMt1RealtimeFeedProcessingCommand::class ,
        Commands\BestMoneySearchGetResponseContactUploadCommand::class,
        Commands\GetFirstPartyRecords::class,
        Commands\ClearRedisKeysWithPatternCommand::class,
        Commands\ReprocessFeedFileCommand::class ,
        Commands\ClearRedisKeysWithPatternCommand::class,
        Commands\ReprocessFeedFileCommand::class ,
        Commands\ScheduledNotificationsCommand::class,
        Commands\BestMoneySearchGetResponseContactUploadCommand::class,
        Commands\UpdateRecordProcessingReportWithErrors::class,
        Commands\PopulateCpmListProfileReportCommand::class ,
        Commands\SyncMT1FeedFieldOrderCommand::class,
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
         * Alerts & Notifications
         */
        $schedule->command('domains:expired')->dailyAt(self::REPORT_TIME); //command DomainExpirationNotification, job class domainExpirationNotifications, job name ExpiredDomains 
        $schedule->command('notify:scheduled')->everyMinute(); //command ScheduledNotificationsCommand, job class ScheduledNotificationQueueJob, job name ScheduledNotificationQueueJob:% 
        $schedule->command('monitors:runtime --mode=monitor --days-back=1 --runtime-threshold=30s')->cron('05 8,16 * * * *'); //job class: RunTimeMonitorJob 

        /**
         * Orphan Adoption
         */
        $orphanFilePath = storage_path('logs')."/adoptOrphans.log";
        #$schedule->command( 'reports:adoptOrphans --maxOrphans=400000 --chunkSize=10000 --queueName=orphanage --chunkDelay=0 --order=newest --maxAttempts=2' )->everyTenMinutes()->sendOutputTo( $orphanFilePath );
        #$schedule->command( 'reports:adoptOrphans --maxOrphans=400000 --chunkSize=10000 --queueName=orphanage --chunkDelay=0 --order=oldest --maxAttempts=2' )->everyTenMinutes()->sendOutputTo( $orphanFilePath );

        /**
         * Suppression Jobs
         */
	    $schedule->command('suppression:downloadESP BlueHornet 5 --runtime-threshold=5m')->cron('0 */4 * * * *'); //job class: DownloadSuppressionFromESP 
        $schedule->command('suppression:downloadESP Maro 5 --runtime-threshold=10m')->cron('0 */4 * * * *'); 
        $schedule->command('suppression:downloadESP Campaigner 5 --runtime-threshold=5m')->cron('0 */4 * * * *'); 
        $schedule->command('suppression:downloadESP EmailDirect 5 --runtime-threshold=5m')->cron('0 */4 * * * *'); 
        $schedule->command('suppression:downloadESP Publicators 5 --runtime-threshold=10m')->cron('0 */4 * * * *'); 
        $schedule->command('suppression:downloadESP Bronto 5 --runtime-threshold=10m')->cron('0 */4 * * * *'); 
        $schedule->command('suppression:downloadESP AWeber 5 --runtime-threshold=1h')->cron('0 */4 * * * *');

        $schedule->command('reports:generateEspUnsubReport --lookback=1')->dailyAt(self::REPORT_TIME); //command ESPUnsubsReport, job class GenerateEspUnsubReport, job name GenerateEspUnsubReport 

        $schedule->command('exportUnsubs emailsForOpensClicks --lookback=15 --runtime-threshold=1h')->dailyAt(self::REPORT_TIME); // command ExportActionsElsewhere, job class ExportActionsJob, job name <report_name>-<date> 
        $schedule->command('exportUnsubs ZxSprintUnsubExport --lookback=1 --runtime-threshold=1m')->dailyAt(self::REPORT_TIME); 
        $schedule->command('exportUnsubs ZxEsuranceUnsubExport --lookback=1 --runtime-threshold=1m')->dailyAt(self::REPORT_TIME); 

        #$unsubFilePath = storage_path( 'logs' ) . "/unsubJobs.log";
        $schedule->command( 'suppression:sendToMT1 3' )->cron('15 */4 * * * *'); //command and job class: SendSuppressionsToMT1, job name FTPSuppressionsToMT1 
        $schedule->command('suppression:exportPublicators 1')->cron('10 */4 * * *'); //command SharePublicatorsUnsubs, job class SharePublicatorsUnsubsJob, job name ExportPublicatorsUnsubs 
        
        /**
         * Campaign Data Daily
         */
        $filePath = storage_path('logs')."/downloadAPI.log";
        $schedule->command('reports:downloadApi BlueHornet --daysBack=5 --runtime-threshold=60s')->hourly()->sendOutputTo($filePath); //command GrabApiEspReports, job class: RetrieveApiReports, job name: RetrieveApiEspReports 
        $schedule->command('reports:downloadApi Campaigner --daysBack=5 --runtime-threshold=60s')->hourly()->sendOutputTo($filePath); 
        #$schedule->command('reports:downloadApi AWeber --daysBack=5 --apiLimit=40 --runtime-threshold=5m')->cron("0 0,6,12,18 * * *")->sendOutputTo($filePath);
        #$schedule->command('reports:downloadApi EmailDirect --daysBack=5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Maro --daysBack=5 --runtime-threshold=60s')->hourly()->sendOutputTo($filePath); 
        $schedule->command('reports:updateMissingMaroCampaigns')->daily()->sendOutputTo($filePath); //command UpdateMissingMaroCampaigns, job class: UpdateMissingMaroCampaignsJob, job name UpdateMissingMaroCampaignsJob 
        //$schedule->command('reports:downloadApi Ymlp --daysBack=5')->hourly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Publicators --daysBack=5 --runtime-threshold=60s')->hourly()->sendOutputTo($filePath); 
        $schedule->command('reports:downloadApi Bronto --daysBack=5 --runtime-threshold=5m')->hourly()->sendOutputTo($filePath); 
        $schedule->command('reports:downloadTrackingData Cake 5 --runtime-threshold=1m')->hourly()->sendOutputTo($filePath); //job class: RetrieveTrackingDataJob
        $schedule->command('process:cfsStats')->cron('0 */4 * * *'); //command PopulateCfsStatsTable , job DataProcessingJob, Job name like: ProcessCfsStats 
        
        

        /**
         * Campaign Data Monthly
         */
        $schedule->command('reports:downloadApi BlueHornet --daysBack=31 --runtime-threshold=5m')->monthly()->sendOutputTo($filePath); 
        $schedule->command('reports:downloadApi Campaigner --daysBack=31 --runtime-threshold=5m')->monthly()->sendOutputTo($filePath); 
        #$schedule->command('reports:downloadApi AWeber --daysBack=31 --apiLimit=200 --runtime-threshold=15m')->monthly()->sendOutputTo($filePath);
        #$schedule->command('reports:downloadApi EmailDirect --daysBack=31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Maro --daysBack=31')->monthly()->sendOutputTo($filePath); 
        //$schedule->command('reports:downloadApi Ymlp --daysBack=31')->monthly()->sendOutputTo($filePath);
        $schedule->command('reports:downloadApi Publicators --daysBack=31 --runtime-threshold=5m')->monthly()->sendOutputTo($filePath); 
        $schedule->command('reports:downloadApi Bronto --daysBack=31 --runtime-threshold=5m')->monthly()->sendOutputTo($filePath); 
        $schedule->command('reports:downloadTrackingData Cake 31 --runtime-threshold=10m')->monthly()->sendOutputTo($filePath); 
        #$schedule->command('aweber:processUniques 31')->monthly()->sendOutputTo($filePath);

        /**
         * Record-level Data
         * Job name like: RetrieveDeliverableReports%
         */
        $deliverableFilePath = storage_path( 'logs' ) . "/downloadDeliverables.log";
        $schedule->command( 'reports:downloadDeliverables Campaigner:delivered 2 Campaigner' )->dailyAt( self::EARLY_DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath ); //command GrabDeliverableReports, job class and name: RetrieveDeliverableReports 
        $schedule->command( 'reports:downloadDeliverables Campaigner:actions 5 Campaigner' )->cron('0 10,20 * * * *'); 
        $schedule->command( 'reports:downloadDeliverables BlueHornet:delivered 3 BlueHornet' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath ); 
        $schedule->command('reports:downloadDeliverables BlueHornet:actions 5 BlueHornet')->cron('0 10,22 * * * *'); 
        #$schedule->command( 'reports:downloadDeliverables EmailDirect 5' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath ); 
        $schedule->command( 'reports:downloadDeliverables Maro 2 Maro' )->cron('0 10,22 * * * *')->sendOutputTo( $deliverableFilePath ); 
        $schedule->command( 'reports:downloadDeliverables Maro:delivered 2 Maro' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath ); 
        //$schedule->command( 'reports:downloadDeliverables Ymlp 5' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );
        $schedule->command( 'reports:downloadDeliverables Bronto:actions 5 Bronto' )->cron('0 10,22 * * * *'); 
        $schedule->command( 'reports:downloadDeliverables Bronto:delivered 2 Bronto' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath ); 
        $schedule->command( 'reports:downloadDeliverables Publicators:delivers 2 Publicators' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath ); 
        $schedule->command( 'reports:downloadDeliverables Publicators:actions 5 Publicators' )->cron('0 10,22 * * * *'); 
        #$schedule->command( 'reports:downloadDeliverables AWeber 5 AWeber' )->dailyAt( self::DELIVERABLE_SCHEDULE_TIME )->sendOutputTo( $deliverableFilePath );

        $schedule->command( 'reports:populateStats')->dailyAt(self::DELIVERABLE_AGGREGATION_TIME)->sendOutputTo($deliverableFilePath); // Job name like: PopulateEmailCampaignStats, PullCakeDeliverableStats 
        $schedule->command('process:useragents')->hourly(); // Job name like: ProcessUserAgents 
        $schedule->command('reports:findIncompleteDeploys --runtime-threshold=10m')->dailyAt(self::DEPLOY_CHECK_TIME); //command CheckDeployStats, job class DataProcessingJob, job name CheckDeployStats 


        /**
         *  Deactivation jobs
         */
        $schedule->command('deactivate:espAccounts')->dailyAt(self::MIDNIGHT); //command DeactivateEspAccounts, no job class //b


        /**
         * Constantly firing.
         *
         */
        $schedule->command('ftp:admin -H ' . config('ssh.servers.mt1_feed_file_server.host') . ' -U ' . config('ssh.servers.mt1_feed_file_server.username') . ' -k ' . config('ssh.servers.mt1_feed_file_server.public_key') . ' -K ' . config('ssh.servers.mt1_feed_file_server.private_key') . ' -u -s Feed')->everyFiveMinutes(); //command FtpAdmin, no job 
        $schedule->command( 'attribution:syncModelsWithNewFeeds' )->everyThirtyMinutes(); //command SyncModelsWithNewFeedsCommand, job class SyncModelsWithNewFeedsJob, job class and name SyncModelsWithNewFeedsJob 

        /**
         *  MT1 data sync jobs
         *  Job names like: ImportMt1, job class DataProcessingJob
         */
        $schedule->command('mt1Import offer --runtime-threshold=20m')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import advertiser --runtime-threshold=1m')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import creative --runtime-threshold=2h')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import from --runtime-threshold=1h')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import subject --runtime-threshold=2h')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import deploy --runtime-threshold=1m')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import offerCreativeMap --runtime-threshold=1h')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import offerFromMap --runtime-threshold=1h')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import offerSubjectMap --runtime-threshold=2h')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import cakeEncryptedLinkMap --runtime-threshold=20m')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import link 2 --runtime-threshold=1h')->cron('0 */2 * * * *');
        $schedule->command('mt1Import feed --runtime-threshold=1m')->cron('0 * * * * *');
        $schedule->command('mt1Import offerTrackingLink --runtime-threshold=10m')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import mailingTemplate --runtime-threshold=30s')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import cakeOffer --runtime-threshold=5m')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import cakeVertical --runtime-threshold=1m')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import cakeOfferMap --runtime-threshold=5m')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import client --runtime-threshold=1m')->dailyAt(self::MT1_SYNC_TIME);
        #$schedule->command('mt1Import vendorSuppressionInfo --runtime-threshold=10m')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import offerSuppressionListMap --runtime-threshold=10m')->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command('mt1Import globalSuppression --runtime-threshold=2h')->cron('45 */4 * * * *');  all mt1 above

        /**
         * Attribution Jobs
         */
         
        // Attribution jobs disabled temporarily until launch
        $schedule->command('runFilter expiration --runtime-threshold=25m')->dailyAt(self::MIDNIGHT); //command RunScheduledFilter, Job class ScheduledFilterResolver, job name: Scheduled Filter Expiration 
        #$schedule->command('attribution:commit daily')->dailyAt(self::ATTRIBUTION_UPDATE_TIME);
        #$schedule->command( 'attribution:conversion -P realtime' )->dailyAt( self::ATTRIBUTION_REPORT_EARLY_UPDATE_TIME ); #early conversion grab & report updating
        #$schedule->command( 'attribution:conversion -P rerun' )->dailyAt( self::ATTRIBUTION_REPORT_UPDATE_TIME ); #daily rerun
        #$schedule->command( 'attribution:conversion -P rerun -d 7' )->weekly(); #weekly rerun
        #$schedule->command( 'attribution:conversion -P rerun -D month -m current' )->monthlyOn( 20 , self::ATTRIBUTION_REPORT_UPDATE_TIME ); #early monthly rerun
        #$schedule->command( 'attribution:conversion -P rerun -D month -m current' )->monthlyOn( 28 , self::ATTRIBUTION_REPORT_UPDATE_TIME ); #monthly rerun
        #$schedule->command( 'attribution:conversion -P rerun -D month -m last' )->monthlyOn( 1 , self::ATTRIBUTION_REPORT_UPDATE_TIME ); #final monthly rerun
        $schedule->command('attribution:validate')->dailyAt(self::FEED_FILE_PROCESS_TIME); // command AttributionFeasibilityValidation, job class AttributionValidationJob, Job name: AttributionValidation //more testing
        
        /**
         *  List profile jobs
         */
        $schedule->command('listprofile:dataEtl --runtime-threshold=5m')->cron('0 1,13,16 * * 1-6 *'); //command S3RedshiftExport, Job names like: %-s3, job class: S3RedshiftExportJob 
        $schedule->command('listprofile:dataEtl --all --runtime-threshold=5m')->cron('0 1 * * 7 *'); 
        $schedule->command('listprofile:optimize')->weekly(); //command VacuumRedshift, job class: VacuumRedshiftJob,  job name OptimizeRedshift 
        $schedule->command('listprofile:aggregateActions --runtime-threshold=6h')->cron('0 0,14 * * * *'); // Job name: ListProfileAggregation, job class: DataProcessingJob 
        $schedule->command('listprofile:contentServerRawStats --runtime-threshold=5m')->hourly(); // Job name: ProcessContentServerRawStats, job class: DataProcessingJob 
        $schedule->command('listprofile:getRecordAgentData 2 --runtime-threshold=40m')->hourly(); // Job name: ContentServerDeviceData, job class: DataProcessingJob 
        $schedule->command('listprofile:baseTables --runtime-threshold=1h')->cron('0 3,12,16 * * 1-6 *'); // Job name like: ListProfileExport%, job class: ListProfileBaseExportJob 
        $schedule->command('listprofile:validateRedshift 1')->cron('0 6 * * * *'); // command RedshiftDataConsistencyValidation, Job names like: DataValidation & upper-case entity, job class: RedshiftDataValidationJob

        /**
         * Feed File Processing
         */
        $schedule->command( 'feedRecords:processMt1BatchFiles --runtime-threshold=15m' )->everyFiveMinutes(); //command ProcessMt1BatchFeedFiles, job class ProcessMt1BatchFeedFilesJob, Job name like: ProcessMt1BatchFeedFilesJob% 
        $schedule->command( 'feedRecords:processMt1RealtimeFiles --runtime-threshold=15m' )->everyMinute(); //command ProcessMt1RealtimeFeedFiles, job class ProcessMt1RealtimeFeedFilesJob, Job name like: ProcessMt1RealtimeFeedFilesJob% 
        $schedule->command( 'feedRecords:processMt1FirstPartyFiles --feedname=unemployment --runtime-threshold=15m' )->everyMinute(); //command ProcessMt1FirstPartyFeedFiles, job class ProcessMt1%, Job name like: ProcessMt1UnemploymentFeedFilesJob%  next 5
        $schedule->command( 'feedRecords:processMt1FirstPartyFiles --feedname=section8 --runtime-threshold=15m' )->everyMinute(); // Job name like: ProcessMt1Section8FeedFilesJob%
        $schedule->command( 'feedRecords:processMt1FirstPartyFiles --feedname=medicaid --runtime-threshold=15m' )->everyMinute(); // Job name like: ProcessMt1MedicaidFeedFilesJob%
        $schedule->command( 'feedRecords:processMt1FirstPartyFiles --feedname=simplyjobs --runtime-threshold=15m' )->everyMinute(); // Job name like: ProcessMt1SimplyJobsFeedFilesJob%
        $schedule->command( 'feedRecords:processMt1FirstPartyFiles --feedname=foodstamps --runtime-threshold=15m' )->everyMinute(); // Job name like: ProcessMt1FoodstampsFeedFilesJob%
        $schedule->command( 'feedRecords:updateCounts' )->dailyAt( self::EARLY_DELIVERABLE_SCHEDULE_TIME ); //command UpdateFeedCounts, job class UpdateFeedCountJob, Job name: UpdateFeedCountJob 
        $schedule->command( 'feedRecords:updateCounts' )->dailyAt( self::UPDATE_SOURCE_COUNTS );
        $schedule->command( 'feedRecords:checkMt1Realtime' )->everyThirtyMinutes(); //command CheckMt1RealtimeFeedProcessingCommand, job class CheckMt1RealtimeFeedProcessingJob, Job name like: CheckMt1RealtimeFeedProcessingJob% 
        $schedule->command( 'feedRecords:checkMt1Batch' )->everyThirtyMinutes(); //command CheckMt1BatchFeedProcessingCommand, job class and name like: CheckMt1BatchFeedProcessingJob% 
        $schedule->command( 'feedRecords:syncFeedFileColumnOrder' )->everyThirtyMinutes(); // Job name like: SyncMT1FeedFieldOrder%

        // Currently commented-out. Waiting for everything going live
        // Process first party feeds, by feed id. This list is dynamic.
        #$schedule->command('feedRecords:firstParty')->cron('*/2 * * * * *'); //command GetFirstPartyRecords, no job 
        
        // Process third party feeds, broken down by starting letter of email address
        $schedule->command('feedRecords:process 3 --startChars=0123456789')->cron('*/2 * * * * *'); // Job names like: FeedProcessing%, Command ProcessFeedRecords, Job ProcessFeedRecordsJob  all :process below
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
        #$schedule->command('feedRecords:reprocessFirstParty 1')->dailyAt(self::DELIVERABLE_AGGREGATION_TIME); //command ReprocessFirstPartyActions, job class FirstPartyReprocessingJob, job name FirstPartyActionsReprocessing 

        // Feed processing reporting
        $schedule->command('feedRecords:updateReportWithErrors --runtime-threshold=10m')->everyFiveMinutes(); //command UpdateRecordProcessingReportWithErrors, job class DataProcessingJob, job name UpdateFeedProcessingErrors

        /**
         * AWeber Jobs
         */
        #$schedule->command('aweber:processUniques 15')->cron("10 0,6,12,18 * * *")->sendOutputTo($filePath); // Job name like: ProcessAweberUniques
        #$schedule->command('aweber:updateAWeberLists' )->dailyAt( self::AWEBER_TIME); // Job name: AWeberUpdateLists
        #$schedule->command('aweber:processAWeberActions')->cron("30 0,6,12,18 * * *")->sendOutputTo($filePath); // Job name: AWeberActionImmigration


        /**
         * Bronto Jobs
        # */

        #$schedule->command("reports:sumBronto")->cron("15 * * * *");

        /**
         *  Data consistency jobs
         *  Job names like DataValidation followed by lower case entity (middle item)
         */
        $schedule->command("dataValidation emails exists --runtime-threshold=1h")->dailyAt(self::MT1_SYNC_TIME); //command DataConsistencyValidation, job class DataConsistencyValidationJob, job name DataValidation-%  and two below
        $schedule->command("dataValidation emailFeedInstances exists --runtime-threshold=30m")->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command("dataValidation emailFeedAssignments value --runtime-threshold=30m")->dailyAt(self::MT1_SYNC_TIME);
        $schedule->command("newActions:process --hoursBack=2 --runtime-threshold=20m")->cron("30 * * * * *"); //command: ProcessNewActionsCommand, job class: ProcessNewActionsJob, Job name like: ProcessNewActions% 
        
        /**
         * Custom Stuff
         */
        #$schedule->command("EspContactUpload:BestMoneySearch")->everyMinute(); //Job name/class: BestMoneySearchGetResponseContactUploadJob, command BestMoneySearchGetResponseContactUploadCommand 
    }
}
