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

    public function saveStats($fromId, $listProfileId, $deployId, $delivers, $opens) {

        DB::connection('reporting_data')->statement(
            "INSERT INTO from_open_rates
            (from_id, list_profile_id, deploy_id, delivers, opens, created_at, updated_at)

            VALUES (:from_id, :list_profile_id, :deploy_id, :delivers, :opens, NOW(), NOW())

            ON DUPLICATE KEY UPDATE
                from_id = from_id,
                list_profile_id = list_profile_id,
                deploy_id = deploy_id,
                opens = :opens2,
                delivers = :delivers2,
                created_at = created_at,
                updated_at = updated_at", [

                    ':from_id' => $fromId,
                    ':list_profile_id' => $listProfileId,
                    ':deploy_id' => $deployId,
                    ':delivers' => $delivers,
                    ':delivers2' => $delivers,
                    ':opens' => $opens,
                    ':opens2' => $opens
                ]);
    }

    public function getFromOfferOpenRate($offerId) {
        $schema = config("database.connections.mysql.database");
        return $this->model
                    ->join("$schema.deploys as d", 'from_open_rates.deploy_id', '=', 'd.id')
                    ->where('d.offer_id', $offerId)
                    ->groupBy('from_open_rates.from_id')
                    ->orderBy("`open_rate`", 'desc')
                    ->select(DB::raw("from_open_rates.from_id, ROUND(SUM(IFNULL(opens, 0)) / SUM(IFNULL(delivers, 0)) * 100, 3) AS `open_rate`"))
                    ->get();
    }

    public function getGeneralFromOpenRateUsingOffer($offerId) {
        $schema = config("database.connections.mysql.database");
        return $this->model
                    ->join("$schema.deploys as d", 'from_open_rates.from_id', '=', 'd.from_id')
                    ->where('d.offer_id', $offerId)
                    ->groupBy('from_open_rates.from_id')
                    ->orderBy("`open_rate`", 'desc')
                    ->select(DB::raw("from_open_rates.from_id, ROUND(SUM(IFNULL(opens, 0)) / SUM(IFNULL(delivers, 0)) * 100, 3) AS `open_rate`"))
                    ->get();
    }
}