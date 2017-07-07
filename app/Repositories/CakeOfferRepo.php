<?php

namespace App\Repositories;

use App\Models\CakeOffer;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;

class CakeOfferRepo {
  
    private $offer;

    public function __construct(CakeOffer $offer) {
        $this->offer = $offer;
    }

    public function updateOrCreate($data) {
        $this->offer->updateOrCreate(['id' => $data['id']], $data);
    }

    public function getVerticalId($id) {
        return $this->offer->where('id', $id)->pluck('vertical_id')->first() ?: 0;
    }

    public function prepareTableForSync() {}

}