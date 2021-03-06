<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class AdvertiserInfoOfferMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'id' => $record['advertiser_id'],
            'name' => $record['advertiser_name'],
            'advertiser_id' => $record['company_id'],
            'status' => $record['status'],
            'is_approved' => ($record['date_approved'] != null),
            'offer_payout_type_id' => $this->mapPayoutType($record['offer_type']),
            'unsub_link' => $record['unsub_link'] ?: '',
            'exclude_days' => $record['exclude_days'],
            'unsub_text' => $record['unsub_text'] ?: '',
            'unsub_type' => $record['unsub_use'],
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
        elseif ('CPS' === $payoutType) {
            return 4;
        }
        else {
            return 5;
        }
    }
}