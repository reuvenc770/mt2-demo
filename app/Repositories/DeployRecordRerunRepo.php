<?php

namespace App\Repositories;


use App\Models\DeployRecordRerun;
use Carbon\Carbon;

class DeployRecordRerunRepo {
    protected $deployRerun;

    public function __construct(DeployRecordRerun $deployRerun) {
        $this->deployRerun = $deployRerun;
    }

    public function insert($data){
        return $this->deployRerun->updateOrCreate($data);
    }

}