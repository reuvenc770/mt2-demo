<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class ClientStatsGroupingClientMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'id' => $record['clientStatsGroupingID'],
            'name' => $record['clientStatsGroupingName']
            // Don't have any other data about clients
        ];
    }
}