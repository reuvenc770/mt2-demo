<?php

namespace App\Services;
use App\Repositories\EmailCampaignStatisticRepo;
use App\Repositories\ContentServerActionRepo;

class UpdateContentServerStatsService {

    private $statsRepo;
    private $csActionRepo;

    public function __construct(ContentServerActionRepo $sourceRepo, EmailCampaignStatisticRepo $statsRepo) {
        $this->statsRepo = $statsRepo;
        $this->sourceRepo = $sourceRepo;
    }

    public function run() {
        $data = $this->sourceRepo->pullAggregatedStats();

        foreach ($data as $row) {
            $this->statsRepo->updateWithContentServerInfo($row);
        }

    }
    
}