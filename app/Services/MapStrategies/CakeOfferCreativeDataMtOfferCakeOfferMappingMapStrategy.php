<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class CakeOfferCreativeDataMtOfferCakeOfferMappingMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'offer_id' => $record['advertiser_id'],
            'cake_offer_id' => $record['offer_id']
        ];
    }
}