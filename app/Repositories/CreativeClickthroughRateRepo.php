<?php

namespace App\Repositories;

use App\Models\ClickThroughRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class CreativeClickthroughRateRepo {
  
    private $model;

    public function __construct(ActionType $model) {
        $this->model = $model;
    } 

    public function saveStats($creativeId, $listProfileId, $deployId, $opens, $clicks) {
        $this->model->updateOrCreate([
            'creative_id' => $creativeId,
            'list_profile_id' => $listProfileId,
            'deploy_id' => $deployId
            ], [
            'creative_id' => $creativeId,
            'list_profile_id' => $listProfileId,
            'deploy_id' => $deployId,
            'opens' => $opens,
            'clicks' => $clicks
            ]);
    }
}