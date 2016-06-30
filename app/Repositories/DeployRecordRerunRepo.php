<?php

namespace App\Repositories;


use App\Models\DeployRecordRerun;
use Carbon\Carbon;
use DB;

class DeployRecordRerunRepo {
    protected $deployRerun;

    public function __construct(DeployRecordRerun $deployRerun) {
        $this->deployRerun = $deployRerun;
    }

    public function loadData($data){
        return $this->deployRerun->updateOrCreate(['deploy_id' => $data['deploy_id']], $data);
    }

    public function getEsps() {
        // return esps that currently appear in the table
        return DB::table('deploy_record_reruns AS d')
                    ->join('esp_accounts AS eac', 'd.esp_account_id', '=', 'eac.id')
                    ->join('esps AS e', 'eac.esp_id', '=', 'e.id')
                    ->where('e.name', '<>', 'EmailDirect')
                    ->select('e.name')
                    ->distinct()
                    ->get();
    }

    public function count() {
        return $this->deployRerun->count();
    }

}