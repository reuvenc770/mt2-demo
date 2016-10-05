<?php

namespace App\Services;

use App\Services\API\CakeDeviceApi;
use App\Repositories\RecordDataRepo;
use DB;


class CakeDeviceService {
    
    private $api;
    private $recordRepo;
    private $data;
    private $pdo;
    const MOBILE_OS = ['Android (Mobile)', 'Ios', 'Windows Phone Os', 'Rim Os', 'Rim Tablet Os', 'Other'];
    const DESKTOP_OS = ['Windows', 'Mac Os', 'Linux'];

    public function __construct(CakeDeviceApi $api, RecordDataRepo $recordRepo) {
        $this->api = $api;
        $this->recordRepo = $recordRepo;
        $this->pdo = DB::connection()->getPdo();
    }

    public function extract($lookback) {
        $this->data = $this->jsonToInsertArray($this->api->sendApiRequest($lookback));
    }

    public function load() {
        $this->recordRepo->updateDeviceData($this->data);
    }

    private function jsonToInsertArray($data) {
        $data = json_decode($this->data, true);
        $output = [];

        foreach($data as $row) {
            $output[] = $this->mapToInsert($row);
        }

        return $output;
    }

    private function mapToInsert($array) {
        $type = $this->getDeviceType($array);
        $device = $this->getDevice($array['os'], $array['device']);

        return '(' . 
            . $this->pdo->quote( $this->subIdToEmailId($array['email_id']) ) . ','
            . $this->pdo->quote($type) . ','
            . $this->pdo->quote($device) . ','
            . $this->pdo->quote($array['carrier']) . ')';
    }

    private function subIdToEmailId($subId) {
        // if the s2 value is useful, it looks like this: 2526053708_0_0_0_0
        $arr = explode('_', $subId);

        if (sizeof($arr) > 0 && is_numeric($arr[0])) {
            return (int)$arr[0];
        }
        else {
            return 0;
        }
    }

    /*
        These are going to be very brittle. 
        Unfortunately, Cake doesn't provide 
        this information at the level of 
        granularity that we want.
    */

    private function getDeviceType($array) {
        if (in_array($array['os'], self::MOBILE_OS)) {
            return 'Mobile';
        }
        elseif (in_array($array['os'], self::DESKTOP_OS)) {
            return 'Desktop'
        }
        elseif ('' === $array['os'] && 'Other' !== $array['device']) {
            return 'Mobile';
        }
        else {
            return 'Unknown';
        }
    }

    private function getDevice($os, $device) {
        if ('' === $os) {
            if (preg_match('/Samsung|LG|Huawei|HTC/i', $device)) {
                return 'Android (Mobile)';
            }
            else {
                return 'Unknown';
            }
        }
        else {
            return $os;
        }
    }
}