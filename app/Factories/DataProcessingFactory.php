<?php

namespace App\Factories;
use App\Repositories\EmailCampaignStatisticRepo;
use App\Repositories\EmailActionsRepo;
use App\Repositories\ActionRepo;
use App\Repositories\TrackingRepo;
use App\Repositories\ContentServerActionRepo;

use App\Models\EmailCampaignStatistic;
use App\Models\EmailAction;
use App\Models\ActionType;
use App\Models\CakeData;
use App\Models\ContentServerAction;

use App\Services\EmailCampaignAggregationService;
use App\Services\CheckDeployService;

/**
 *  Create different services for generic data processing/OLTP
 *  Class DataProcessingFactory
 *  @package App\Factories
 */

class DataProcessingFactory {

    public static function create($name) {
        switch($name) {
            case 'PopulateAttributionRecordReport':
                return self::createAttributionRecordAggregationService();

            case 'PopulateEmailCampaignStats':
                return self::createEmailCampaignAggregationService();

            case('PullCakeDeliverableStats'):
                return self::createTrackingDeliverableService();

            case('UpdateContentServerStats'):
                return self::createUpdateContentServerStatsService();

            case('ProcessUserAgents'):
                return self::createUserAgentProcessingService();

            case('CheckDeployStats'):
                return self::createCheckDeployStatsService();

            case('PublicatorsActions'):
                return self::createPublicatorsActionService();
            case('ProcessCfsStats'):
                return self::createProcessCfsStatsService();

            case ('ImportMt1Advertisers'):
                $mt1Name = 'CompanyInfo';
                $mt2Name = 'Advertiser';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case('ImportMt1Offers'):
                $mt1Name = 'AdvertiserInfo';
                $mt2Name = 'Offer';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case('ImportMt1Creatives'):
                $mt1Name = 'Creative';
                $mt2Name = 'Creative';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case('ImportMt1Froms'):
                $mt1Name = 'AdvertiserFrom';
                $mt2Name = 'From';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case('ImportMt1Subjects'):
                $mt1Name = 'AdvertiserSubject';
                $mt2Name = 'Subject';
                return self::createMt1ImportService($mt1Name, $mt2Name);
                
            case('ImportMT1ListProfiles'):
                $mt1Name = "UniqueProfile";
                $mt2Name = "ListProfile";
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case ('ImportMT1Deploys'):
                $mt1Name = 'EspAdvertiserJoin';
                $mt2Name = 'Deploy';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case('ImportMT1OfferCreativeMapping'):
                $mt1Name = "Creative";
                $mt2Name = "OfferCreativeMap";
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case('ImportMT1OfferFromMapping'):
                $mt1Name = "AdvertiserFrom";
                $mt2Name = "OfferFromMap";
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case('ImportMT1OfferSubjectMapping'):
                $mt1Name = "AdvertiserSubject";
                $mt2Name = "OfferSubjectMap";
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case('ImportMt1CakeEncryptionMapping'):
                $mt1Name = "AffiliateCakeEncryption";
                $mt2Name = "CakeEncryptedLink";
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case ("ImportMt1Links"):
                $mt1Name = 'Link';
                $mt2Name = 'Link';
                return self::createMt1ImportService($mt1Name, $mt2Name);
            
            case ('ImportMt1Feeds'):
                $mt1Name = "User";
                $mt2Name = "Feed";
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case ('ImportMt1OfferTracking'):
                $mt1Name = 'AdvertiserTracking';
                $mt2Name = 'OfferTrackingLink';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case ('ImportMt1MailingTemplate'):
                $mt1Name = 'BrandTemplate';
                $mt2Name = 'MailingTemplate';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            default:
                throw new \Exception("Data processing service {$name} does not exist");
        }
    }

    private static function createAttributionRecordAggregationService () {
        $attrRecordRepo = \App::make( \App\Repositories\Attribution\RecordReportRepo::class );
        $cakeConversion = \App::make( \App\Services\CakeConversionService::class );
        $attrEmailActionsRepo = \App::make( \App\Repositories\Attribution\AttributionEmailActionsRepo::class );
        $emailRecordService = \App::make( \App\Services\EmailRecordService::class );
        $suppressionService = \App::make( \App\Services\SuppressionService::class );
        $standardReportService = \App\Factories\ServiceFactory::createStandardReportService();
        $etlPickupRepo = \App::make( \App\Repositories\EtlPickupRepo::class );

        return new \App\Services\Attribution\RecordAggregatorService(
            $attrRecordRepo ,
            $cakeConversion ,
            $attrEmailActionsRepo ,
            $emailRecordService ,
            $suppressionService ,
            $standardReportService ,
            $etlPickupRepo
        );
    }

    private static function createEmailCampaignAggregationService() {
        $actionType = new ActionType;
        $actionTypeRepo = new ActionRepo($actionType);
        $actions = new EmailAction();
        $actionsRepo = new EmailActionsRepo($actions);
        $stats = new EmailCampaignStatistic();
        $statsRepo = new EmailCampaignStatisticRepo($stats);
        $actionMap = $actionTypeRepo->getMap();

        $etlPickup = new \App\Models\EtlPickup();
        $etlPickupRepo = new \App\Repositories\EtlPickupRepo($etlPickup);

        return new EmailCampaignAggregationService($statsRepo, $actionsRepo, $etlPickupRepo, $actionMap);
    }

    private static function createTrackingDeliverableService() {
        $statsModel = new EmailCampaignStatistic();
        $statsRepo = new EmailCampaignStatisticRepo($statsModel);

        $trackingModel = new CakeData();
        $trackingRepo = new TrackingRepo($trackingModel);

        return new \App\Services\TrackingDeliverableService($trackingRepo, $statsRepo);        
    }

    private static function createUpdateContentServerStatsService() {
        $contentActions = new ContentServerAction();
        $contentActionsRepo = new ContentServerActionRepo($contentActions);

        // need repo for email_campaign_statistics
        $statsModel = new EmailCampaignStatistic();
        $statsRepo = new EmailCampaignStatisticRepo($statsModel);
        return new \App\Services\UpdateContentServerStatsService($contentActionsRepo, $statsRepo);      
    }

    private static function createUserAgentProcessingService() {
        // feed off a source of new user agents
        $sourceModel = new CakeData();
        $sourceRepo = new TrackingRepo($sourceModel);
        $userAgent = new \App\Models\UserAgentString();
        $userAgentRepo = new \App\Repositories\UserAgentStringRepo($userAgent);
        return new \App\Services\UserAgentProcessingService($sourceRepo, $userAgentRepo);
    }

    private static function createCheckDeployStatsService() {
        $actions = new EmailAction();
        $actionsRepo = new EmailActionsRepo($actions);
        $rerun = new \App\Models\DeployRecordRerun();
        $rerunRepo = new \App\Repositories\DeployRecordRerunRepo($rerun);
        return new CheckDeployService($actionsRepo, $rerunRepo);
    }

    private static function createPublicatorsActionService() {
        $actions = new EmailAction();
        $actionsRepo = new EmailActionsRepo($actions);
        return new \App\Services\PublicatorsActionService($actionsRepo, $actionsRepo);
    }

    private static function createProcessCfsStatsService() {
        $eaj = new \App\Models\MT1Models\EspAdvertiserJoin();
        $eajRepo = new \App\Repositories\MT1Repositories\EspAdvertiserJoinRepo($eaj);

        $stdModel = new \App\Models\StandardReport();
        $stdRepo = new \App\Repositories\StandardApiReportRepo($stdModel);

        $crModel = new \App\Models\CreativeClickthroughRate();
        $crRepo = new \App\Repositories\CreativeClickthroughRateRepo($crModel);

        $subjModel = new \App\Models\SubjectOpenRate();
        $subjRepo = new \App\Repositories\SubjectOpenRateRepo($subjModel);

        $fromModel = new \App\Models\FromOpenRate();
        $fromRepo = new \App\Repositories\FromOpenRateRepo($fromModel);

        return new \App\Services\PopulateCfsStatsService($eajRepo, $stdRepo, $crRepo, $fromRepo, $subjRepo);
    }

    private static function createMt1ImportService($mt1Name, $mt2Name) {

        $mt1ModelName = "App\\Models\\MT1Models\\{$mt1Name}";
        $mt1RepoName = "App\\Repositories\\MT1Repositories\\{$mt1Name}Repo";
        $mt1Repo = new $mt1RepoName( new $mt1ModelName() );

        $mt2ModelName = "App\\Models\\{$mt2Name}";
        $mt2RepoName = "App\\Repositories\\{$mt2Name}Repo";
        $mt2Repo = new $mt2RepoName( new $mt2ModelName() );

        $mapStrategyName = "App\\Services\\MapStrategies\\{$mt1Name}{$mt2Name}MapStrategy";
        $mapStrategy = \App::make($mapStrategyName);

        return new \App\Services\ImportMt1DataService($mt1Repo, $mt2Repo, $mapStrategy);
    }

}
