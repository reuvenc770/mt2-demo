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

    public function saveStats($subjectId, $listProfileCombineId, $deployId, $delivers, $opens) {

        DB::connection('reporting_data')->statement(
            "INSERT INTO subject_open_rates
            (subject_id, list_profile_combine_id, deploy_id, delivers, opens, created_at, updated_at)

            VALUES (:subject_id, :list_profile_combine_id, :deploy_id, :delivers, :opens, NOW(), NOW())

            ON DUPLICATE KEY UPDATE
                subject_id = subject_id,
                list_profile_combine_id = list_profile_combine_id,
                deploy_id = deploy_id,
                opens = :opens2,
                delivers = :delivers2,
                created_at = created_at,
                updated_at = updated_at", [

                    ':subject_id' => $subjectId,
                    ':list_profile_id' => $listProfileCombineId,
                    ':deploy_id' => $deployId,
                    ':delivers' => $delivers,
                    ':delivers2' => $delivers,
                    ':opens' => $opens,
                    ':opens2' => $opens
                ]);
    }



    public function getGeneralSubjectOpenRateUsingOffer($offerId) {
        $schema = config("database.connections.mysql.database");
        return $this->model
                    ->join("$schema.deploys as d", 'subject_open_rates.subject_id', '=', 'd.subject_id')
                    ->leftJoin("$schema.subjects as s", 'subject_open_rates.subject_id', '=', 's.id')
                    ->where('d.offer_id', $offerId)
                    ->groupBy('subject_open_rates.subject_id', 'name')
                    ->orderBy("open_rate", 'desc')
                    ->select(DB::raw("subject_open_rates.subject_id, s.subject_line as name, ROUND(SUM(IFNULL(opens, 0)) / SUM(IFNULL(delivers, 0)) * 100, 3) AS open_rate"))
                    ->get();
    }
}