<?php

namespace App\Repositories;

use App\Models\MtOfferCakeOfferMapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;

class MtOfferCakeOfferMappingRepo {
  
    private $model;

    public function __construct(MtOfferCakeOfferMapping $model) {
        $this->model = $model;
    }

    public function updateOrCreate($data) {
        $this->model->updateOrCreate(['offer_id' => $data['offer_id'], 'cake_offer_id' => $data['cake_offer_id']], $data);
    }

}