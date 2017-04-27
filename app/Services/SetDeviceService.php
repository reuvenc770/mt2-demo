<?php

namespace App\Services;

use App\Repositories\ContentServerStatsRawRepo;
use App\Repositories\EmailAttributableFeedLatestDataRepo;
use DB;
use App\Services\ServiceTraits\IdentifyUserAgent;
use App\Services\API\CakeDeviceApi;

class SetDeviceService {
    use IdentifyUserAgent;
    
    private $statsRepo;
    private $recordRepo;
    private $emailFeedDataRepo;
    private $data;
    private $pdo;
    private $deviceApi;
    private $carrierInfo;
    private $emailCarrierCache = [];

    public function __construct(ContentServerStatsRawRepo $statsRepo, CakeDeviceApi $deviceApi, EmailAttributableFeedLatestDataRepo $emailFeedDataRepo) {
        $this->statsRepo = $statsRepo;
        $this->emailFeedDataRepo = $emailFeedDataRepo;
        $this->deviceApi = $deviceApi;
        $this->pdo = DB::connection()->getPdo();
        $this->setAgent();
    }

    public function extract($lookback) {
        $this->carrierInfo = json_decode($this->deviceApi->sendApiRequest()->getBody(), true);
        $this->data = $this->statsRepo->pullEmailUserAgents($lookback);

    }

    public function load() {
        foreach($this->carrierInfo as $cakeEvent) {
            // subid_2 and carrier
            
            $emailId = $this->getEmailId($cakeEvent['subid_2']);

            if (!is_null($emailId) && $emailId > 0) {
                $emailId = (int)$emailId;
                $this->emailCarrierCache[$emailId] = $cakeEvent['carrier'];
            }
        }

        foreach($this->data->cursor() as $row) {
            $carrier = isset($this->emailCarrierCache[$row->email_id]) ? $this->emailCarrierCache[$row->email_id] : '';
            $row = $this->mapToRow($row, $carrier);
            $this->emailFeedDataRepo->batchUpdateDeviceData($row);
        }
        
        $this->emailFeedDataRepo->cleanUpDeviceData();
    }

    private function mapToRow($row, $carrier) {
        return [
            'email_id' => $row->email_id,
            'feed_id' => $row->feed_id,
            'device_type' => $this->getDeviceType($row->user_agent),
            'device_name' => $this->assignDeviceToFamily($row->user_agent),
            'carrier' => $carrier
        ];
    }

    private function getEmailId($subId2) {
        // 2820556461_0_0_0_0 or 0_0_0_0_0
        // Return the first number.
        if ('' === $subId2) {
            return null;
        }
        else {
            return explode('_', $subId2)[0];
        }

    }

}