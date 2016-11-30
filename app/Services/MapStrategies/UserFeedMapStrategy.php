<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class UserFeedMapStrategy implements IMapStrategy {

    public function map($row) {
        return [
            'id' => $row['user_id'],
            'name' => $row['username'],
            'client_id' => $row['clientStatsGroupingID'] ?: 1,
            'party' => $this->getPartyLevel($row),
            'short_name' => $row['company'],
            'password' => $row['rt_pw'] ,
            'status' => $this->convertFeedStatus($row['status']),
            'source_url' => $row['clientRecordSourceURL'] ?: '',
            'frequency' => $row['upl_freq'],
            'type_id' => $row['clientTypeId'] ?: 1,
            'country_id' => $this->getMt2CountryId($row['countryID']),
            'created_at' => $row['create_datetime'],
            'updated_at' => $row['overall_updated']
            // don't have vertical information yet
        ];
    }


    private function convertFeedStatus($status) {
        return $status === 'A' ? 'Active' : 'Inactive';
    }

    private function getPartyLevel($row) {
        // For the time being, 1st party data shows up as 'non-Orange'
        if ($row['OrangeClient'] === 'Y') {
            return 3;
        }
        else {
            return 1;
        }
    }
    
    private function getMt2CountryId($mt1CountryId) {
        if (1 === (int)$mt1CountryId) {
            return 1;
        }
        elseif (235 === (int)$mt1CountryId) {
            return 2;
        }
        elseif (null === $mt1CountryId) {
            return 1; // default
        }
        else {
            return 0; // this is an error for now
        }
    }
}
