<?php

namespace App\Repositories;

use App\Models\ContentServerStatsRaw;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class ContentServerStatsRawRepo {
  
    private $model;

    public function __construct(ContentServerStatsRaw $model) {
        $this->model = $model;
    }

    public function nextNRows($start, $offset) {
        return $this->model
            ->where('id', '>=', $start)
            ->orderBy('id')
            ->skip($offset)
            ->first()['id'];
    }

    public function pullAggregatedActions($start, $end) {
        return DB::select("SELECT
                email_id,
                link_id,
                sub_aff_id as deploy_id,
                DATE(action_datetime) as date,
                IF(SUM(IF(action_id = 1, 1, 0)) > 0, 1, 0) as has_cs_open,
                IF(SUM(IF(action_id = 2, 1, 0)) > 0, 1, 0) as has_cs_click
            FROM
                content_server_stats_raws
            WHERE
                id > :start
                AND
                id <= :end
            GROUP BY
                email_id, link_id, deploy_id, date)", [
            ':start' => $start,
            ':end' => $end
        ]);
    }
}