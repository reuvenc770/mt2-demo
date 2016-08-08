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

    public function saveStats($creativeId, $listProfileId, $deployId, $opens, $clicks) {

        DB::connection('reporting_data')->statement(
            "INSERT INTO creative_clickthrough_rates
            (creative_id, list_profile_id, deploy_id, opens, clicks, created_at, updated_at)

            VALUES (:creative_id, :list_profile_id, :deploy_id, :opens, :clicks, NOW(), NOW())

            ON DUPLICATE KEY UPDATE
                creative_id = creative_id,
                list_profile_id = list_profile_id,
                deploy_id = deploy_id,
                opens = :opens2,
                clicks = :clicks2,
                created_at = created_at,
                updated_at = updated_at", [

                    ':creative_id' => $creativeId,
                    ':list_profile_id' => $listProfileId,
                    ':deploy_id' => $deployId,
                    ':opens' => $opens,
                    ':opens2' => $opens,
                    ':clicks' => $clicks,
                    ':clicks2' => $clicks
                ]);
    }

    public function getCreativeOfferClickRate($offerId) {
        $schema = config("database.connections.mysql.database");
        return $this->model
                    ->join("$schema.deploys as d", 'creative_clickthrough_rates.deploy_id', '=', 'd.id')
                    ->leftJoin("$schema.creatives as c", 'creative_clickthrough_rates.creative.id', '=', 'c.id')
                    ->where('d.offer_id', $offerId)
                    ->groupBy('creative_clickthrough_rates.creative_id', 'name')
                    ->orderBy("click_rate", 'desc')
                    ->select(DB::raw("creative_clickthrough_rates.creative_id, c.file_name as name, ROUND(SUM(IFNULL(clicks, 0)) / SUM(IFNULL(opens, 0)) * 100, 3) AS click_rate"))
                    ->get();
    }

        public function getGeneralCreativeClickRateUsingOffer($offerId) {
        $schema = config("database.connections.mysql.database");
        return $this->model
                    ->join("$schema.deploys as d", 'creative_clickthrough_rates.creative_id', '=', 'd.creative_id')
                    ->leftJoin("$schema.creatives as c", 'creative_clickthrough_rates.creative.id', '=', 'c.id')
                    ->where('d.offer_id', $offerId)
                    ->groupBy('creative_clickthrough_rates.creative_id', 'name')
                    ->orderBy("click_rate", 'desc')
                    ->select(DB::raw("creative_clickthrough_rates.creative_id, c.file_name as name, ROUND(SUM(IFNULL(clicks, 0)) / SUM(IFNULL(opens, 0)) * 100, 3) AS click_rate"))
                    ->get();
    }
}