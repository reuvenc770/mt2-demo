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
            'status' => $this->convertFeedStatus($row['status']),
            'source_url' => $row['clientRecordSourceURL'] ?: '',
            'frequency' => $row['upl_freq'],
            'type_id' => $row['clientTypeId'] ?: 1,
            'country_id' => $row['countryID'] ?: 1,
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
}