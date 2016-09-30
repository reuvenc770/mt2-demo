<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class CakeOfferCakeOfferMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'id' => $record['offerID'],
            'name' => $record['offerName'],
            'vertical_id' => $record['verticalID'],
            'cake_advertiser_id' => $record['advertiserID']
        ];
    }
}