<?php

namespace App\Repositories;


use App\Models\DeployRecordRerun;
use Carbon\Carbon;

class DeployActionRerunRepo {
    protected $deployRerun;

    public function __construct(DeployRecordRerun $deployRerun) {
        $this->deployRerun = $deployRerun;
    }

    public function insert($data){
        return $this->deployAction->create($data);
    }

}