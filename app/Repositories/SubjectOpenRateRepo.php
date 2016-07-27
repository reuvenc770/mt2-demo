<?php

namespace App\Repositories;

use App\Models\SubjectOpenRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class SubjectOpenRateRepo {
  
    private $model;

    public function __construct(SubjectOpenRate $model) {
        $this->model = $model;
    } 

    public function saveStats($subjectId, $listProfileId, $deployId, $delivers, $opens) {
        $this->model->updateOrCreate([
            'subject_id' => $subjectId,
            'list_profile_id' => $listProfileId,
            'deploy_id' => $deployId
            ], [
            'subject_id' => $subjectId,
            'list_profile_id' => $listProfileId,
            'deploy_id' => $deployId,
            'delivers' => $delivers,
            'opens' => $opens
            ]);
    }
}