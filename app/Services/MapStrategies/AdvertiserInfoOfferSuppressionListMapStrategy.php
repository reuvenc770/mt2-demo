<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class AdvertiserInfoOfferSuppressionListMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'offer_id' => $record['advertiser_id'],
            'suppression_list_id' => $record['vendor_supp_list_id']
        ];
    }
}