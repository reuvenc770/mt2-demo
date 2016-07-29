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

}