<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class AdvertiserTrackingOfferTrackingLinkMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'offer_id' => $record['advertiser_id'],
            'link_num' => $record['link_num'],
            'link_id' => $record['link_id'],
            'url' => $this->transformUrl($record['url']),
            'approved_by' => $record['approved_by'],
            'date_approved' => $record['date_approved'],
        ];
    }

    private function transformUrl($url) {
        return preg_replace('/s1=\d+/', 's1={{DEPLOY_ID}}', $url);
    }
}