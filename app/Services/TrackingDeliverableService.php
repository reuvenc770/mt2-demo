<?php

namespace App\Services;
use App\Repositories\EmailCampaignStatisticRepo;
use App\Repositories\TrackingRepo;
use App\Services\AbstractEtlService;

class TrackingDeliverableService extends AbstractEtlService {

    public function __construct(TrackingRepo $trackingRepo, EmailCampaignStatisticRepo $statsRepo) {
        parent::__construct($trackingRepo, $statsRepo);
    }

    public function extract($lookback = null) {
        $lookback = $lookback ? $lookback : config('jobs.cake.lookback');
        $this->data = $this->sourceRepo->pullDeliverables($lookback);
    }

    public function load() {
        foreach ($this->data as $row) {
            $emailId = $row['email_id'];
            $deployId = $row['deploy_id'];
            $row = $this->transform($row);
            $this->targetRepo->updateWithTrackingInfo($emailId, $deployId, $row);
        }
    }
    
    protected function transform($row) {
        return [
            'trk_first_click_datetime' => $row['first_click'],
            'trk_last_click_datetime' => $row['last_click'],
            'trk_total_clicks' => $row['clicks'],
            'user_agent_id' => $row['uas_id']
        ];
    }
}