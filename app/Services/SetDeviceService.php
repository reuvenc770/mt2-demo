<?php

namespace App\Services;

use App\Repositories\ContentServerStatsRawRepo;
use App\Repositories\EmailAttributableFeedLatestDataRepo;
use DB;
use App\Services\ServiceTraits\IdentifyUserAgent;

// TODO: switch third party repos with the commented-out repos below

class SetDeviceService {
    use IdentifyUserAgent;
    
    private $statsRepo;
    private $recordRepo;
    private $emailFeedDataRepo;
    private $data;
    private $pdo;

    public function __construct(ContentServerStatsRawRepo $statsRepo, EmailAttributableFeedLatestDataRepo $emailFeedDataRepo) {
        $this->statsRepo = $statsRepo;
        $this->emailFeedDataRepo = $emailFeedDataRepo;
        $this->pdo = DB::connection()->getPdo();
        $this->setAgent();
    }

    public function extract($lookback) {
        $this->data = $this->statsRepo->pullEmailUserAgents($lookback);
    }

    public function load() {
        foreach($this->data->cursor() as $row) {
            $row = $this->mapToRow($row);
            $this->emailFeedDataRepo->batchUpdateDeviceData($row);
        }
        
        $this->emailFeedDataRepo->cleanUpDeviceData();
    }

    private function mapToRow($row) {
        return [
            'email_id' => $row->email_id,
            'feed_id' => $row->feed_id,
            'device_type' => $this->getDeviceType($row->user_agent),
            'device_name' => $this->assignDeviceToFamily($row->user_agent)
        ];
    }

}