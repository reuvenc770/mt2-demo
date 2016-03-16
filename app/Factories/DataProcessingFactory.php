<?php

namespace App\Factories;
use App\Repositories\EmailCampaignStatisticRepo;
use App\Repositories\EmailActionsRepo;
use App\Repositories\ActionRepo;

use App\Models\EmailCampaignStatistic;
use App\Models\EmailAction;
use App\Models\ActionType;

use App\Services\EmailCampaignAggregationService;


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
                $actionRepo = new EmailActionsRepo($actionModel);
                $statsModel = new EmailCampaignStatistic();
                $statsRepo = new EmailCampaignStatisticRepo($model);

                $actionMap = $actionTypeRepo->getMap();

                return new EmailCampaignAggregationService($statsRepo, $actionsRepo, $actionMap, $lookback);
                break;

            default:
                throw new \Exception("Data processing service {$name} does not exist");
        }
    }
}