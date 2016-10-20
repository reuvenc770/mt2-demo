<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class CakeVerticalCakeVerticalMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'id' => $record['verticalID'],
            'name' => $record['verticalName']
        ];
    }
}