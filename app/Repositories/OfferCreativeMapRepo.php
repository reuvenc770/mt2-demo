<?php

namespace App\Repositories;

use App\Models\OfferCreativeMap;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class OfferCreativeMapRepo extends AbstractDataSyncRepo{
  
    private $model;

    public function __construct(OfferCreativeMap $model) {
        $this->model = $model;
    } 

    public function updateOrCreate($data) {
        $this->model->updateOrCreate([
            'offer_id' => $data['offer_id'], 
            'creative_id' => $data['creative_id']
        ], $data);
    }

    public function bulkInsert()
    {
        //Interface Adherence and maybe update later
    }

}