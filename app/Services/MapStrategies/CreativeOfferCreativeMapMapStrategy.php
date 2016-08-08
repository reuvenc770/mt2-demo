<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class CreativeOfferCreativeMapMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'offer_id' => $record['advertiser_id'],
            'creative_id' => $record['creative_id']
        ];
    }
}