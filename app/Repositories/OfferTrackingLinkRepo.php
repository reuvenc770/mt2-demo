<?php

namespace App\Repositories;

use App\Models\OfferTrackingLink;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class OfferTrackingLinkRepo {
    
    private $tracking;

    public function __construct(OfferTrackingLink $tracking) {
        $this->tracking = $tracking;
    }

    public function updateOrCreate($data) {
        $this->tracking->updateOrCreate([
                'offer_id' => $data['offer_id'],
                'link_num' => $data['link_num']
            ]
            , $data);
    }
}