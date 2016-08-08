<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class AdvertiserFromOfferFromMapMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'offer_id' => $record['advertiser_id'],
            'from_id' => $record['from_id']
        ];
    }
}