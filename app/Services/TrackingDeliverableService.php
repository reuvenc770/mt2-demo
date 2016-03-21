<?php

namespace App\Services;
use App\Repositories\EmailCampaignStatisticRepo;
use App\Repositories\TrackingRepo;

class TrackingDeliverableService {

    private $statsRepo;
    private $trackingRepo;

    public function __construct(EmailCampaignStatisticRepo $statsRepo, TrackingRepo $trackingRepo) {
        $this->statsRepo = $statsRepo;
        $this->trackingRepo = $trackingRepo;
    }

    public function run($lookback) {
        $data = $this->trackingRepo->pullDeliverables($lookback);
        echo "Running for cake data" . PHP_EOL;
        #var_dump($data);

        foreach ($data as $row) {
            $this->statsRepo->updateWithTrackingInfo($row);
        }
    }

    private function insert() {

    }
    
    private function insertSegmented() {}
}