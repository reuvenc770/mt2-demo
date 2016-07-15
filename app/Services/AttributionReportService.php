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
     *  Function getStatsStats
     *  Get numbers for specified data
     *  @param Array $params
     *  $params has the fields 
     *  Required:'date'
     *  Optional: 'deployId', 'modelId', 'feedId'
     *  @return Laravel Collection
     */

    public function getStats($params) {

        if (!isset($params['date'])) {
            throw new Exception('Attribution model requires a start date');
        }

        $date = $params['date'];
        $deployId = isset($params['deployId']) ? (int)$params['deployId'] : null;
        $modelId = isset($params['modelId']) ? (int)$params['modelId'] : null;
        $feedId = isset($params['feedId']) ? (int)$params['feedId'] : null;

        if (!$deployId && !$modelId && !$feedId) {
            // no deploy, model, or feed specified - get all current stats
            return $this->statsRepo->getAllOfficialStats($date);
        }
        elseif ($deployId && !$modelId && !$feedId) {
            // deploy set but no model specified - get live stats for this deploy
            return $this->statsRepo->getOfficialDeployStats($date, $deployId);
        }
        elseif (!$deployId && $modelId && !$feedId) {
            // no deploy or feed but a model specified - get model data for date range
            return $this->statsRepo->getModelStats($date, $modelId);
        }
        elseif ($deployId && $modelId && !$feedId) {
            // deploy and model, but no feed specified - get model data for this deploy
            return $this->statsRepo->getModelDeployStats($date, $modelId, $deployId);
        }
        elseif (!$deployId && !$modelId && $feedId) {
            // feed, but no deploy or model specified - get all official data for this feed 
            return $this->statsRepo->getOfficialFeedStats($date, $feedId);
        }
        elseif ($deployId && !$modelId && $feedId) {
            // deploy and feed, but no model specified - get all official data for this deploy and feed
            return $this->statsRepo->getOfficialDeployFeedStats($date, $deployId, $feedId);
        }
        elseif (!$deployId && $modelId && $feedId) {
            // feed but no deploy or model - get all model stats for a particular feed
            return $this->statsRepo->getModelFeedStats($date, $modelId, $deployId, $feedId);
        }
        elseif ($deployId && $modelId && $feedId) {
            // deploy, model, and feed specified - get model data for this particular combination
            return $this->statsRepo->getModelDeployFeedStats($date, $modelId, $deployId, $feedId);
        }

    }
}