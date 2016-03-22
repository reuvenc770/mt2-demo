<?php

namespace App\Factories;
use App\Repositories\EmailCampaignStatisticRepo;
use App\Repositories\EmailActionsRepo;
use App\Repositories\ActionRepo;
use App\Repositories\TrackingRepo;

use App\Models\EmailCampaignStatistic;
use App\Models\EmailAction;
use App\Models\ActionType;
use App\Models\CakeData;

use App\Services\EmailCampaignAggregationService;
use App\Services\TrackingDeliverableService;

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
                break;

            case('PullCakeDeliverableStats'):
                $statsModel = new EmailCampaignStatistic();
                $statsRepo = new EmailCampaignStatisticRepo($statsModel);

                $trackingModel = new CakeData();
                $trackingRepo = new TrackingRepo($trackingModel);

                return new TrackingDeliverableService($statsRepo, $trackingRepo);
                break;

            default:
                throw new \Exception("Data processing service {$name} does not exist");
        }
    }
}