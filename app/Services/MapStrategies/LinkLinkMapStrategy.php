<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class LinkLinkMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'id' => $record['link_id'],
            'url' => $record['refurl'],
            'created_at' => $record['date_added']
        ];
    }
}