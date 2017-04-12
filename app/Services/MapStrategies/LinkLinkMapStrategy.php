<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;
use Carbon\Carbon;

class LinkLinkMapStrategy implements IMapStrategy {

    public function map($record) {
        $createDatetime = $record['date_added'] === '0000-00-00 00:00:00' ? 
            Carbon::now()->toDateTimeString() : $record['date_added'];

        return [
            'id' => $record['link_id'],
            'url' => $record['refurl'],
            'created_at' => $createDatetime
        ];
    }
}