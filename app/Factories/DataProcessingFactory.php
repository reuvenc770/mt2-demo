<?php

namespace App\Factories;
use App\Models\Deploy;
use App\Repositories\DeployRepo;
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
                return \App::make(\App\Services\UserAgentProcessingService::class);

            case('CheckDeployStats'):
                return self::createCheckDeployStatsService();

            case('PublicatorsActions'):
                return self::createPublicatorsActionService();

            case('ProcessCfsStats'):
                return self::createProcessCfsStatsService();

            case('ListProfileAggregation'):
                return \App::make(\App\Services\ListProfileActionAggregationService::class);

            case('ContentServerDeviceData'):
                return \App::make(\App\Services\SetDeviceService::class);

            case('UpdateUserActions'):
                return \App::make(\App\Services\UserActionStatusService::class);

            case ('ProcessContentServerRawStats'):
                $service = \App::make(\App\Services\ProcessRawContentServerStats::class);
                $service->setJobName('ProcessContentServerRawStats');
                return $service;

            # Export from MT2 to MT1

            case('Mt1Export-email_list'):
                $mt2Name = 'Email';
                $mt1Name = 'EmailList';
                return self::createMt2ExportService($mt2Name, $mt1Name);

            case('Mt1Export-user'):
                $mt2Name = 'Feed';
                $mt1Name = 'User';
                return self::createMt2ExportService($mt2Name, $mt1Name); 

            case('Mt1Export-EspAdvertiserJoin'):
                $mt2Name = 'Deploy';
                $mt1Name = 'EspAdvertiserJoin';
                return self::createMt2ExportService($mt2Name, $mt1Name);

            case('Mt1Export-link'):
                $mt2Name = 'Link';
                $mt1Name = 'Link';
                return self::createMt2ExportService($mt2Name, $mt1Name);

            # Import from MT1

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

            case ('ImportMt1CakeOffers'):
                $mt1Name = 'CakeOffer';
                $mt2Name = 'CakeOffer';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case('ImportMt1CakeVertical'):
                $mt1Name = 'CakeVertical';
                $mt2Name = 'CakeVertical';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case('ImportMt1CakeOfferMapping'):
                $mt1Name = 'CakeOfferCreativeData';
                $mt2Name = 'MtOfferCakeOfferMapping';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case('ImportMt1Client'):
                $mt1Name = 'ClientStatsGrouping';
                $mt2Name = 'Client';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case('ImportMt1VendorSuppression'):
                $mt1Name = 'VendorSuppList';
                $mt2Name = 'SuppressionListSuppression';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case('ImportMt1VendorSuppressionInfo'):
                $mt1Name = 'VendorSuppListInfo';
                $mt2Name = 'SuppressionList';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case ('ImportMt1OfferSuppressionListMap'):
                $mt1Name = 'AdvertiserInfo';
                $mt2Name = 'OfferSuppressionList';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            case ('ImportMt1GlobalSuppression'):
                $mt1Name = 'SuppressListOrange';
                $mt2Name = 'SuppressionGlobalOrange';
                return self::createMt1ImportService($mt1Name, $mt2Name);

            default:
                throw new \Exception("Data processing service {$name} does not exist");
        }
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
        $deploy = new Deploy();
        $deployRepo = new DeployRepo($deploy);
        $stdModel = new \App\Models\StandardReport();
        $stdRepo = new \App\Repositories\StandardApiReportRepo($stdModel);

        $crModel = new \App\Models\CreativeClickthroughRate();
        $crRepo = new \App\Repositories\CreativeClickthroughRateRepo($crModel);

        $subjModel = new \App\Models\SubjectOpenRate();
        $subjRepo = new \App\Repositories\SubjectOpenRateRepo($subjModel);

        $fromModel = new \App\Models\FromOpenRate();
        $fromRepo = new \App\Repositories\FromOpenRateRepo($fromModel);

        return new \App\Services\PopulateCfsStatsService($deployRepo, $stdRepo, $crRepo, $fromRepo, $subjRepo);
    }

    private static function createMt1ImportService($mt1Name, $mt2Name) {
        $mt1RepoName = "App\\Repositories\\MT1Repositories\\{$mt1Name}Repo";
        $mt1Repo = \App::make($mt1RepoName);

        $mt2RepoName = "App\\Repositories\\{$mt2Name}Repo";
        $mt2Repo = \App::make($mt2RepoName);

        $mapStrategyName = "App\\Services\\MapStrategies\\{$mt1Name}{$mt2Name}MapStrategy";
        $mapStrategy = \App::make($mapStrategyName);

        return new \App\Services\ImportMt1DataService($mt1Repo, $mt2Repo, $mapStrategy);
    }

    private static function createMt2ExportService($mt2Name, $mt1Name) {
        $mt2RepoName = $mt2Name . 'Repo';
        $mt1RepoName = $mt1Name . 'Repo';

        $mt2Repo = \App::make("App\\Repositories\\$mt2RepoName");
        $mt1Repo = \App::make("App\\Repositories\\MT1Repositories\\$mt1RepoName");

        $pickupRepo = \App::make(\App\Repositories\EtlPickupRepo::class);
        $processName = $mt2Name . $mt1Name;

        return new \App\Services\Mt2ToMt1ExportService($mt2Repo, $mt1Repo, $pickupRepo, $processName);
    }

}
