<?php

namespace App\Services;

use Exception;
use App\Repositories\AttributionStatsCalculationRepo;

class AttributionReportService {
    
    private $statsRepo;

    public function __construct(AttributionStatsCalculationRepo $statsRepo) {
        $this->statsRepo = $statsRepo;
    }

    /**
     *  Function getDeployStats
     *  Get numbers for specified deploys and models by date range
     *  @param Array $params
     *  $params has the fields 'date', 'deployId', 'modelId'
     *  @return Laravel Collection
     */

    public function getDeployStats($params) {

        if (! isset($params['date']) {
            throw new Exception('Attribution model requires a start date');
        }

        $date = $params['date'];
        $deployId = isset($params['deployId']) ? (int)$params['deployId'] : null;
        $modelId = isset($params['modelId']) ? (int)$params['modelId'] : null;

        if (!$deployId && !$modelId) {
            // no deploy or model specified - get live stats grouped by deploy
            return $this->statsRepo->getAllOfficialDeployStats($date);
        }
        elseif ($deployId && !$modelId) {
            // deploy but no model - get live stats for a deploy
            return $this->statsRepo->getDeployOfficialStats($date, $deployId);
        }
        elseif (!$deployId && $modelId) {
            // no deploy but a model - get model data for all deploys
            return $this->statsRepo->getModelDeployStats($date, $modelId);
        }
        elseif ($deployId && $modelId) {
            // deploy and model - get model stats for a deploy
            return $this->statsRepo->getDeployModelStats($date, $deployId, $modelId);
        }
    }

    /**
     *  Function getFeedStats
     *  Get numbers for specified deploys and models by date range
     *  @param Array $params
     *  $params has the fields 'date', 'feedId', 'modelId'
     *  @return Laravel Collection
     */

    public function getFeedStats($params) {

        if (! isset($params['date']) {
            throw new Exception('Attribution model requires a start date');
        }

        $date = $params['date'];
        $feedId = isset($params['feedId']) ? (int)$params['feedId'] : null;
        $modelId = isset($params['modelId']) ? (int)$params['modelId'] : null;

        if (!$feedId && !$modelId) {
            // no feed or model specified - get live stats grouped by feed
            return $this->statsRepo->getAllOfficialFeedStats($date);
        }
        elseif ($feedId && !$modelId) {
            // feed but no model - get live stats for a feed
            return $this->statsRepo->getFeedOfficialStats($date, $feedId);
        }
        elseif (!$feedId && $modelId) {
            // no feed but a model - get model data for all feeds
            return $this->statsRepo->getModelFeedStats($date, $modelId);
        }
        elseif ($feedId && $modelId) {
            // feed and model - get model stats for a feed
            return $this->statsRepo->getFeedModelStats($date, $feedId, $modelId);
        }

    }
}