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
use App\Services\TrackingDeliverableService;
use App\Services\UpdateContentServerStatsService;

/**
 *  Create different services for generic data processing/OLTP
 *  Class DataProcessingFactory
 *  @package App\Factories
 */

class DataProcessingFactory {

    public static function create($name, $lookback) {
        switch($name) {
            case 'PopulateEmailCampaignStats':
                $actionTypeModel = new ActionType();
                $actionTypeRepo = new ActionRepo($actionTypeModel);
                $actionModel = new EmailAction();
                $actionsRepo = new EmailActionsRepo($actionModel);
                $statsModel = new EmailCampaignStatistic();
                $statsRepo = new EmailCampaignStatisticRepo($statsModel);
                $actionMap = $actionTypeRepo->getMap();

                return new EmailCampaignAggregationService($statsRepo, $actionsRepo, $actionMap, $lookback);

            case('PullCakeDeliverableStats'):
                $statsModel = new EmailCampaignStatistic();
                $statsRepo = new EmailCampaignStatisticRepo($statsModel);

                $trackingModel = new CakeData();
                $trackingRepo = new TrackingRepo($trackingModel);

                return new TrackingDeliverableService($statsRepo, $trackingRepo);

            case('UpdateContentServerStats'):

                // need repo for content_server_actions
                $contentActions = new ContentServerAction();
                $contentActionsRepo = new ContentServerActionRepo($contentActions);

                // need repo for email_campaign_statistics
                $statsModel = new EmailCampaignStatistic();
                $statsRepo = new EmailCampaignStatisticRepo($statsModel);
                return new UpdateContentServerStatsService($contentActionsRepo, $statsRepo);

            default:
                throw new \Exception("Data processing service {$name} does not exist");
        }
    }
}