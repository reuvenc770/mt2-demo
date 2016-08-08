<?php

namespace App\Repositories;

use App\Models\OfferFromMap;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class OfferFromMapRepo {
  
    private $model;

    public function __construct(OfferFromMap $model) {
        $this->model = $model;
    } 

    public function updateOrCreate($data) {
        $this->model->updateOrCreate([
            'offer_id' => $data['offer_id'], 
            'from_id' => $data['from_id']
        ], $data);
    }

}