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

}