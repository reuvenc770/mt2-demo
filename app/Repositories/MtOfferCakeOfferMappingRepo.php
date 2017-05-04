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
        $this->model->insert($data);
    }

    public function prepareTableForSync() {
        $this->model->truncate();
    }

}