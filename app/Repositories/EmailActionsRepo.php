<?php

namespace App\Repositories;

use App\Models\EmailAction;
use DB;
use PDO;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;;

/**
 *
 */
class EmailActionsRepo {
  
    private $actions;

    public function __construct(EmailAction $actions) {
        $this->actions = $actions;
    }

    public function maxId() {
        return $this->actions->orderBy('id', 'desc')->first()['id'];
    }

    public function insertAction($data) {
        $this->actions->insert($data);
    }

    public function pullLimitedActionsInLast($lookback, $limit) {
        return $this->actions
            ->where('id', '>', $lookback)
            ->orderBy('id', 'asc')
            ->take($limit)
            ->get();
    }

    public function pullAggregatedActions($startPoint, $limit) {

        return DB::connection('reporting_data')->select("SELECT
          email_id,
          campaign_id,
          GROUP_CONCAT(types.name ORDER BY datetime DESC SEPARATOR ',') AS statuses,
          GROUP_CONCAT(CASE WHEN types.name = 'opener' THEN datetime ELSE NULL END ORDER BY datetime ASC SEPARATOR ',') AS esp_first_open_datetimes,
          GROUP_CONCAT(CASE WHEN types.name = 'opener' THEN datetime ELSE NULL END ORDER BY datetime DESC SEPARATOR ',') AS esp_last_open_datetimes,
          COUNT(CASE WHEN (types.name = 'opener' OR types.name='clicker') THEN 1 ELSE 0 END) AS opens_counted,
           
          GROUP_CONCAT(CASE WHEN types.name = 'clicker' THEN datetime ELSE NULL END ORDER BY datetime ASC SEPARATOR ',') AS esp_first_click_datetimes,
          GROUP_CONCAT(CASE WHEN types.name = 'clicker' THEN datetime ELSE NULL END ORDER BY datetime DESC SEPARATOR ',') AS esp_last_click_datetimes, # this will need to update last_open sometimes
          COUNT(CASE WHEN types.name='clicker' THEN 1 ELSE 0 END) AS clicks_counted,
          SUM(CASE WHEN (types.name = 'unsubscriber' OR types.name = 'complainer') THEN 1 ELSE 0 END) AS unsubscribed,
          MAX(ea.id) AS max_id          
        FROM
          mt2_reports.email_actions ea
          INNER JOIN mt2_reports.action_types types ON ea.action_id = types.id
        WHERE
          ea.id BETWEEN :startPoint AND :endPoint
          AND
          types.name <> 'deliverable'
        GROUP BY
          ea.email_id, ea.campaign_id", 
            array(
                ':startPoint' => $startPoint,
                ':endPoint' => $startPoint + $limit
            )
        );
    }
}