<?php

namespace App\Services;

use App\Repositories\ContentServerStatsRawRepo;
use App\Repositories\RecordDataRepo;
use DB;
use App\Services\ServiceTraits\IdentifyUserAgent;


class SetDeviceService {
    use IdentifyUserAgent;
    
    private $statsRepo;
    private $recordRepo;
    private $data;
    private $pdo;

    public function __construct(ContentServerStatsRawRepo $statsRepo, RecordDataRepo $recordRepo) {
        $this->statsRepo = $statsRepo;
        $this->recordRepo = $recordRepo;
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
        }
        
        $this->recordRepo->cleanupDeviceData();
    }

    private function mapToRow($row) {
        return [
            'email_id' => $row->email_id,
            'device_type' => $this->getDeviceType($row->user_agent),
            'device_name' => $this->assignDeviceToFamily($row->user_agent)
        ];
    }

}