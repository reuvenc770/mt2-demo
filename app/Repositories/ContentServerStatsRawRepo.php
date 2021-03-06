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

    public function maxId() {
        return $this->model->orderBy('id', 'desc')->first()['id'];
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
                eid as email_id,
                email_address,
                lower_case_md5,
                upper_case_md5,
                e.email_domain_id,
                link_id,
                sub_aff_id as deploy_id,
                DATE(action_datetime) as date,
                IF(SUM(IF(action_id = 1, 1, 0)) > 0, 1, 0) as has_cs_open,
                IF(SUM(IF(action_id = 2, 1, 0)) > 0, 1, 0) as has_cs_click
            FROM
                content_server_stats_raws r
                INNER JOIN emails e ON r.eid = e.id
            WHERE
                r.id > :start
                AND
                r.id <= :end
            GROUP BY
                email_id, link_id, deploy_id, date

            UNION

            SELECT
                em.id as email_id,
                em.email_address,
                em.lower_case_md5,
                em.upper_case_md5,
                em.email_domain_id,
                link_id,
                sub_aff_id as deploy_id,
                DATE(action_datetime) as date,
                IF(SUM(IF(action_id = 1, 1, 0)) > 0, 1, 0) as has_cs_open,
                IF(SUM(IF(action_id = 2, 1, 0)) > 0, 1, 0) as has_cs_click
            FROM
                content_server_stats_raws r
                LEFT JOIN emails e ON r.eid = e.id
                INNER JOIN emails em ON substr(query_string, locate('em=', query_string) + 3, locate('&', substr(query_string, locate('em=', query_string) + 3)) -1) = em.email_address
            WHERE
                r.id > :start2
                AND
                r.id <= :end2
                AND
                e.id IS NULL
            GROUP BY
                email_id, link_id, deploy_id, date", [
            ':start' => $start,
            ':end' => $end,
            ':start2' => $start,
            ':end2' => $end,
        ]);
    }

    public function pullUserAgents($lookback) {
        return $this->model
                    ->select('user_agent')
                    ->where('action_datetime', '>=', DB::raw("now() - INTERVAL $lookback MINUTE"));
    }

    public function pullEmailUserAgents($lookback) {
        $attrDb = config('database.connections.attribution.database');

        return $this->model
                    ->select('eid as email_id', 'feed_id', 'user_agent')
                    ->join("$attrDb.email_feed_assignments as efa", 'eid', '=', 'efa.email_id')
                    ->whereRaw("action_datetime >= CURDATE() - INTERVAL $lookback HOUR")
                    ->orderBy('action_datetime', 'ASC');
    }
}
