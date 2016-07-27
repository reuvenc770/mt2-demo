<?php

namespace App\Repositories;

use App\Models\FromOpenRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class FromOpenRateRepo {
  
    private $model;

    public function __construct(ActionType $model) {
        $this->model = $model;
    } 

    public function saveStats($fromId, $listProfileId, $deployId, $delivers, $opens) {
        $this->model->updateOrCreate([
            'from_id' => $fromId,
            'list_profile_id' => $listProfileId,
            'deploy_id' => $deployId
            ], [
            'from_id' => $fromId,
            'list_profile_id' => $listProfileId,
            'deploy_id' => $deployId,
            'delivers' => $delivers,
            'opens' => $opens
            ]);
    }
}