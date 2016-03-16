<?php

use App\Repositories\EmailCampaignStatisticRepo;

class EmailCampaignAggregationService {

    private $statsRepo;
    private $actionsRepo;
    private $lookback;
    private $actionMap;

    public function __construct(EmailCampaignStatistic $statsRepo, EmailActionsRepo $actionsRepo, $actionMap, $lookback) {
        $this->statsRepo = $statsRepo;
        $this->lookback = $lookback;
        $this->actionsRepo = $actionsRepo;
        $this->actionMap = $actionMap;
    }

    public function run() {
    /*
        1. Start at beginning of lookback period.
        2. Get all rows from email_actions (add key to created_at)
        3. For each row:
            get email_id, campaign_id, and action
            check if row exists in table. If not, insert row. If it does, update appropriate fields
    */
        $data = $this->actionsRepo->pullActionsInLast($lookback);

        foreach ($data as $row) {
            $actionType = $this->actionMap[$row['action_id']];
            $this->statsRepo->insertOrUpdate($row), $actionType;
        }


    }
}