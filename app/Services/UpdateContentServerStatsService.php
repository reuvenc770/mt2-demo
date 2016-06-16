<?php

namespace App\Services;
use App\Repositories\EmailCampaignStatisticRepo;
use App\Repositories\ContentServerActionRepo;
use App\Services\AbstractEtlService;

class UpdateContentServerStatsService extends AbstractEtlService {

    public function __construct(ContentServerActionRepo $sourceRepo, EmailCampaignStatisticRepo $statsRepo) {
        parent::__construct($sourceRepo, $statsRepo);
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
            'mt_first_open_datetime' => $row['first_open'],
            'mt_last_open_datetime' => $row['last_open'],
            'mt_total_opens' => $row['clicks'],
            'mt_first_click_datetime' => $row['first_click'],
            'mt_last_click_datetime' => $row['last_click'],
            'mt_total_clicks' => $row['clicks']
        ];
    }
}