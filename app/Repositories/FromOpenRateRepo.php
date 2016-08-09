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
        DB::enableQueryLog();
        $test = $this->model
                    ->leftJoin("offer_from_maps as ofm", 'from_open_rates.from_id', '=', 'ofm.from_id')
                    ->leftJoin("$schema.froms as f", 'from_open_rates.from_id', '=', 'f.id')
                    ->where('ofm.offer_id', $offerId)
                    ->where('f.status', 1)
                    ->where('f.is_approved', 1)
                    ->groupBy('from_open_rates.from_id', 'name')
                    ->orderBy("open_rate", 'desc')
                    ->select(DB::raw("from_open_rates.from_id, f.from_line as name, ROUND(SUM(IFNULL(opens, 0)) / SUM(IFNULL(delivers, 0)) * 100, 3) AS open_rate"))->toSql();
        dd($test);

    }

    public function getGeneralFromOpenRateUsingOffer($offerId) {
        $schema = config("database.connections.mysql.database");
        return $this->model
                    ->join("$schema.deploys as d", 'from_open_rates.from_id', '=', 'd.from_id')
                    ->leftJoin("$schema.froms as f", 'from_open_rates.from_id', '=', 'f.id')
                    ->where('d.offer_id', $offerId)
                    ->groupBy('from_open_rates.from_id', 'name')
                    ->orderBy("open_rate", 'desc')
                    ->select(DB::raw("from_open_rates.from_id, f.from_line as name, ROUND(SUM(IFNULL(opens, 0)) / SUM(IFNULL(delivers, 0)) * 100, 3) AS open_rate"))
                    ->get();
    }
}