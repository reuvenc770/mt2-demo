<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class AdvertiserInfoOfferMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'id' => $record['advertiser_id'],
            'name' => $record['advertiser_name'],
            'advertiser_id' => $record['company_id'],
            'offer_payout_type_id' => $this->mapPayoutType($record['offer_type'])
        ];
    }

    protected function mapPayoutType($payoutType) {
        if ('CPM' === $payoutType) {
            return 1;
        }
        elseif ('CPC' === $payoutType) {
            return 2;
        }
        elseif ('CPA' === $payoutType) {
            return 3;
        }
        else {
            return 4;
        }
    }
}