<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class CakeCreativeOfferJoinMtOfferCakeOfferMappingMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'offer_id' => $record['advertiser_id'],
            'cake_offer_id' => $record['offerID']
        ];
    }
}