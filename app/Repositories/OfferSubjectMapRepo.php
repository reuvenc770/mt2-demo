<?php

namespace App\Repositories;

use App\Models\OfferSubjectMap;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class OfferSubjectMapRepo {
  
    private $model;

    public function __construct(OfferSubjectMap $model) {
        $this->model = $model;
    } 

    public function updateOrCreate($data) {
        $this->model->updateOrCreate([
            'offer_id' => $data['offer_id'], 
            'subject_id' => $data['subject_id']
        ], $data);
    }

}