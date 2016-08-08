<?php

namespace App\Repositories;

use App\Models\Offer;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class OfferRepo {
  
    private $offer;

    public function __construct(Offer $offer) {
        $this->offer = $offer;
    }

    public function updateOrCreate($data) {
        $this->offer->updateOrCreate(['id' => $data['id']], $data);
    }

    public function getAdvertiserName($offerId) {
        $result = $this->offer
             ->join('advertisers as a', 'offers.advertiser_id', '=', 'a.id')
             ->where('offers.id', $offerId)
             ->first();

        if ($result) {
            return $result->name;
        }
        else {
            return '';
        }
    }

}