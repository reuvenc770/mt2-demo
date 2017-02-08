<?php

namespace App\Services;

use App\Services\API\ContentServerStatsRawRepo;
use App\Repositories\RecordDataRepo;
use App\Repositories\EmailAttributableFeedLatestDataRepo;
use DB;
use App\Services\ServiceTraits\IdentifyUserAgent;


class SetDeviceService {
    use IdentifyUserAgent;
    
    private $statsRepo;
    private $recordRepo;
    private $emailFeedDataRepo;
    private $data;
    private $pdo;

    public function __construct(ContentServerStatsRawRepo $statsRepo, RecordDataRepo $recordRepo, EmailAttributableFeedLatestDataRepo $emailFeedDataRepo) {
        $this->statsRepo = $statsRepo;
        $this->recordRepo = $recordRepo;
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
            $this->recordRepo->batchUpdateDeviceData($row);
            #$this->emailFeedDataRepo->batchUpdateDeviceData($row);
        }
        
        $this->recordRepo->cleanupDeviceData();
        #$this->emailFeedDataRepo->cleanUpDeviceData();
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