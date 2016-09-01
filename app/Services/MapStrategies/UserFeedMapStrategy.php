<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class UserFeedMapStrategy implements IMapStrategy {

    public function map($row) {
        return [
            'id' => $row['user_id'],
            'name' => $row['username'],
            'party' => $this->getPartyLevel($row),
            'address' => $row['address'],
            'address2' => $row['address2'],
            'city' => $row['city'],
            'state' => $row['state'],
            'zip' => $row['zip'],
            'phone' => $row['phone'],
            'email_address' => $row['email_addr'],
            'status' => $this->convertFeedStatus($row['status']),
            'source_url' => $row['clientRecordSourceURL'] ?: '',
            'created_at' => $row['create_datetime'],
            'updated_at' => $row['overall_updated']
        ];
    }


    private function convertFeedStatus($status) {
        return $status === 'A' ? 'Active' : 'Deleted';
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