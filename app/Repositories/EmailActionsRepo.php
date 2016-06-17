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

    public function nextNRows($start, $offset) {
        return $this->actions
            ->where('id', '>=', $start)
            ->orderBy('id')
            ->skip($offset)
            ->first()['id'];
    }

    public function pullLimitedActionsInLast($lookback, $limit) {
        return $this->actions
            ->where('id', '>', $lookback)
            ->orderBy('id', 'asc')
            ->take($limit)
            ->get();
    }

    public function pullAggregatedActions($startPoint, $endPoint) {

        return DB::connection('reporting_data')->select("SELECT
          email_id,
          deploy_id,
          GROUP_CONCAT(types.name ORDER BY datetime DESC SEPARATOR ',') AS statuses,
          GROUP_CONCAT(CASE WHEN types.name = 'opener' THEN datetime ELSE NULL END ORDER BY datetime ASC SEPARATOR ',') AS esp_first_open_datetimes,
          GROUP_CONCAT(CASE WHEN types.name = 'opener' THEN datetime ELSE NULL END ORDER BY datetime DESC SEPARATOR ',') AS esp_last_open_datetimes,
          SUM(CASE WHEN (types.name = 'opener' OR types.name='clicker') THEN 1 ELSE 0 END) AS opens_counted,
           
          GROUP_CONCAT(CASE WHEN types.name = 'clicker' THEN datetime ELSE NULL END ORDER BY datetime ASC SEPARATOR ',') AS esp_first_click_datetimes,
          GROUP_CONCAT(CASE WHEN types.name = 'clicker' THEN datetime ELSE NULL END ORDER BY datetime DESC SEPARATOR ',') AS esp_last_click_datetimes, # this will need to update last_open sometimes
          SUM(CASE WHEN types.name='clicker' THEN 1 ELSE 0 END) AS clicks_counted,
          SUM(CASE WHEN (types.name = 'unsubscriber' OR types.name = 'complainer') THEN 1 ELSE 0 END) AS unsubscribed,
          MAX(ea.id) AS max_id          
        FROM
          mt2_reports.email_actions ea
          INNER JOIN mt2_reports.action_types types ON ea.action_id = types.id
        WHERE
          ea.id BETWEEN :startPoint AND :endPoint
        GROUP BY
          ea.email_id, ea.deploy_id", 
            array(
                ':startPoint' => $startPoint,
                ':endPoint' => $endPoint
            )
        );
    }

    public function pullIncompleteDeploys($lookback) {
        return DB::select("SELECT
              deploy_id, 
              sr.datetime,
              ROUND((SUM(ea.delivered) - sr.delivered) / sr.delivered, 3) AS 'delivers_diff',
              ROUND((SUM(opens) - sr.e_opens) / sr.e_opens, 3) AS 'opens_diff',
              ROUND((SUM(clicks) - sr.e_clicks) / sr.e_clicks, 3) AS 'clicks_diff'
            FROM
              mt2_reports.standard_reports sr 
                LEFT JOIN (SELECT
                deploy_id,
                SUM(CASE WHEN action_id = 4 THEN 1 ELSE 0 END) AS delivered,
                SUM(CASE WHEN (action_id = 1 OR action_id = 2) THEN 1 ELSE 0 END) AS opens,
                SUM(CASE WHEN (action_id = 2) THEN 1 ELSE 0 END) AS clicks
              FROM
                mt2_reports.email_actions ea
                INNER JOIN mt2_reports.standard_reports std ON ea.deploy_id = std.external_deploy_id
              WHERE
                action_id IN (1, 2, 4)
                AND
                std.datetime BETWEEN CURDATE() - INTERVAL $lookback DAY AND CURDATE() - INTERVAL 2 DAY
              GROUP BY
                deploy_id) ea ON ea.deploy_id = sr.external_deploy_id
               
            WHERE
              sr.datetime BETWEEN CURDATE() - INTERVAL $lookback DAY AND CURDATE() - INTERVAL 2 DAY

            GROUP BY
                deploy_id
            HAVING
              `delivers_diff` < -.075
              || `opens_diff`  < -.075
              || `clicks_diff` < -.075");
    }

    public function pullEspAccount($espAccounts, $date) {
        $espAccountString = implode(',', $espAccounts);

        return DB::select("SELECT
            DISTINCT email_address, 
            email_id
            FROM
                mt2_reports.email_actions ea
                INNER JOIN mt2_data.emails e ON ea.email_id = e.id
            WHERE
                esp_account_id IN ($espAccountString)
                AND
                ea.action_id IN (1,2)
                AND
                ea.created_at >= $date");
    }
}