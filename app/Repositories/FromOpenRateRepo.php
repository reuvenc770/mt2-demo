<?php

namespace App\Repositories;

use App\Models\FromOpenRate;
use DB;

/**
 *
 */
class FromOpenRateRepo {
  
    private $model;

    public function __construct(FromOpenRate $model) {
        $this->model = $model;
    } 

    public function saveStats($fromId, $listProfileCombineId, $deployId, $delivers, $opens) {

        DB::connection('reporting_data')->statement(
            "INSERT INTO from_open_rates
            (from_id, list_profile_combine_id, deploy_id, delivers, opens, created_at, updated_at)

            VALUES (:from_id, :list_profile_combine_id, :deploy_id, :delivers, :opens, NOW(), NOW())

            ON DUPLICATE KEY UPDATE
                from_id = from_id,
                list_profile_combine_id = list_profile_combine_id,
                deploy_id = deploy_id,
                opens = :opens2,
                delivers = :delivers2,
                created_at = created_at,
                updated_at = updated_at", [

                    ':from_id' => $fromId,
                    ':list_profile_combine_id' => $listProfileCombineId,
                    ':deploy_id' => $deployId,
                    ':delivers' => $delivers,
                    ':delivers2' => $delivers,
                    ':opens' => $opens,
                    ':opens2' => $opens
                ]);
    }

}