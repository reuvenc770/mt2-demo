<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class VendorSuppListInfoSuppressionListMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'id' => $record['list_id'],
            'name' => $record['list_name'],
            'suppression_list_type' => 1, // These should all be type 1 for now - advertiser suppression
            'status' => $record['status']
        ];
    }
}