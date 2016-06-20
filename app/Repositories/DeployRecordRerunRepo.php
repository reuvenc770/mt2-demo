<?php

namespace App\Repositories;


use App\Models\DeployRecordRerun;
use Carbon\Carbon;

class DeployRecordRerunRepo {
    protected $deployRerun;

    public function __construct(DeployRecordRerun $deployRerun) {
        $this->deployRerun = $deployRerun;
    }

    public function loadData($data){
        return $this->deployRerun->updateOrCreate(['deploy_id' => $data['deploy_id']], $data);
    }

}