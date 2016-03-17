<?php

namespace App\Services;
use App\Repositories\EmailCampaignStatisticRepo;
use App\Repositories\EmailActionsRepo;

class EmailCampaignAggregationService {

    private $statsRepo;
    private $actionsRepo;
    private $lookback;
    private $actionMap;

    public function __construct(EmailCampaignStatisticRepo $statsRepo, EmailActionsRepo $actionsRepo, $actionMap, $lookback) {
        $this->statsRepo = $statsRepo;
        $this->lookback = $lookback;
        $this->actionsRepo = $actionsRepo;
        $this->actionMap = $actionMap;
    }

    public function run() {
        $data = $this->actionsRepo->pullActionsInLast($this->lookback);
        foreach ($data as $row) {
            $actionType = $this->actionMap[$row['action_id']];
            $this->statsRepo->insertOrUpdate($row, $actionType);
        }
    }
    
}