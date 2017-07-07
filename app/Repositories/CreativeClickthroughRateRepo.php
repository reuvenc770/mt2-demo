<?php

namespace App\Repositories;

use App\Models\CreativeClickthroughRate;
use DB;

/**
 *
 */
class CreativeClickthroughRateRepo {
  
    private $model;

    public function __construct(CreativeClickthroughRate $model) {
        $this->model = $model;
    } 

    public function saveStats($creativeId, $listProfileCombineId, $deployId, $delivers, $opens, $clicks) {

        DB::connection('reporting_data')->statement(
            "INSERT INTO creative_clickthrough_rates
            (creative_id, list_profile_combine_id, deploy_id, delivers, opens, clicks, created_at, updated_at)

            VALUES (:creative_id, :list_profile_combine_id, :deploy_id, :delivers, :opens, :clicks, NOW(), NOW())

            ON DUPLICATE KEY UPDATE
                creative_id = creative_id,
                list_profile_combine_id = list_profile_combine_id,
                deploy_id = deploy_id,
                delivers = :delivers2,
                opens = :opens2,
                clicks = :clicks2,
                created_at = created_at,
                updated_at = updated_at", [

                    ':creative_id' => $creativeId,
                    ':list_profile_combine_id' => $listProfileCombineId,
                    ':deploy_id' => $deployId,
                    ':opens' => $opens,
                    ':opens2' => $opens,
                    ':clicks' => $clicks,
                    ':clicks2' => $clicks,
                    ':delivers' => $delivers,
                    ':delivers2' => $delivers
                ]);
    }
    
}