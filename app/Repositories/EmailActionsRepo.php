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
  
    protected $actions;

    public function __construct(EmailAction $actions) {
        $this->actions = $actions;
    }

    public function getByDateRange ( $dateRange = null ) {
        if( is_null( $dateRange ) ) {
            $startDate = Carbon::now()->startOfDay()->toDateTimeString();
            $endDate = Carbon::now()->endOfDay()->toDateTimeString();
        } else {
            $startDate = $dateRange[ 'start' ];
            $endDate = $dateRange[ 'end' ];
        }

        return $this->actions
                    ->whereBetween( 'datetime' , [ $startDate , $endDate ] )
                    ->get();

    }

    public function getAggregatedByDateRange ( $dateRange = null ) {
        if( is_null( $dateRange ) ) {
            $startDate = Carbon::now()->startOfDay()->toDateTimeString();
            $endDate = Carbon::now()->endOfDay()->toDateTimeString();
        } else {
            $startDate = $dateRange[ 'start' ];
            $endDate = $dateRange[ 'end' ];
        }

        return DB::connection( 'reporting_data' ) ->select(
            "SELECT
                DATE( datetime ) AS `date` ,
                email_id ,
                deploy_id ,
                SUM( IF ( action_id = 4 , 1 , 0 ) ) AS `delivered`,
                SUM( IF( action_id = 1 , 1 , 0 ) ) AS `opened` ,
                SUM( IF( action_id = 2 , 1 , 0 ) ) AS `clicked`
            FROM
                email_actions
            WHERE
                datetime BETWEEN :start AND :end 
            GROUP BY
                email_id ,
                deploy_id ,
                `date`"
            , [
                ':start' => $startDate ,
                ':end' => $endDate
            ] );
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

    public function pullAggregatedReportActions($startPoint, $endPoint) {

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

    public function pullAggregatedActions($daysBack) {

        return $this->actions
                    ->select('email_actions.email_id', 'deploy_id', 
                        DB::raw('DATE(datetime) AS date'), 
                        DB::raw('SUM(IF(action_id = 4, 1, 0)) as deliveries'), 
                        DB::raw('SUM(IF(action_id = 1, 1, 0)) as opens'), 
                        DB::raw('SUM(IF(action_id = 2, 1, 0)) as clicks'))
                    ->whereRaw("email_actions.datetime >= CURDATE() - INTERVAL $daysBack DAY")
                    ->groupBy('email_actions.email_id', 'email_actions.deploy_id', 'date');
    }

    public function pullAggregatedListProfileActions($start, $end) {
        return DB::select("SELECT
            ea.email_id,
            ea.deploy_id,
            ea.date,
            e.email_address,
            ed.id as email_domain_id,
            dg.id as email_domain_group_id,
            IFNULL(d.offer_id, 0) as offer_id,
            IFNULL(cv.id, 0) as cake_vertical_id,
            ea.deliveries,
            ea.opens,
            ea.clicks,
            ea.created_at,
            ea.updated_at
   
        FROM (SELECT
                email_id,
                deploy_id,
                DATE(datetime) as date,
                
                SUM(IF(action_id = 4, 1, 0)) as deliveries,
                SUM(IF(action_id = 1, 1, 0)) as opens,
                SUM(IF(action_id = 2, 1, 0)) as clicks,
                NOW() as created_at,
                NOW() as updated_at
            FROM
                mt2_reports.email_actions
            WHERE
                id between :start and :end
            GROUP BY
                 email_id, deploy_id, `date`) ea

            INNER JOIN emails e on ea.email_id = e.id
            INNER JOIN email_domains ed on e.email_domain_id = ed.id
            LEFT JOIN domain_groups dg on ed.domain_group_id = dg.id
            LEFT JOIN deploys d on ea.deploy_id = d.id
            LEFT JOIN mt_offer_cake_offer_mappings cake_map ON d.offer_id = cake_map.offer_id
            LEFT JOIN cake_offers co ON cake_map.cake_offer_id = co.id
            LEFT JOIN cake_verticals cv ON co.vertical_id = cv.id", 
            array(
                ':start' => $start,
                ':end' => $end
            ));
    }

    public function pullIncompleteDeploys($lookback) {
        return DB::select("SELECT
              deploy_id, 
              sr.datetime,
              sr.esp_account_id,
              sr.esp_internal_id,
              ROUND((SUM(ea.delivered) - SUM(sr.delivered)) / SUM(sr.delivered), 3) AS 'delivers_diff',
              ROUND((SUM(opens) - SUM(sr.e_opens)) / SUM(sr.e_opens), 3) AS 'opens_diff',
              ROUND((SUM(clicks) - SUM(sr.e_clicks)) / SUM(sr.e_clicks), 3) AS 'clicks_diff'
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
                std.datetime BETWEEN CURDATE() - INTERVAL $lookback DAY AND CURDATE() - INTERVAL 5 DAY
              GROUP BY
                deploy_id) ea ON ea.deploy_id = sr.external_deploy_id
               
            WHERE
              sr.datetime BETWEEN CURDATE() - INTERVAL $lookback DAY AND CURDATE() - INTERVAL 5 DAY

            GROUP BY
                deploy_id, sr.datetime, sr.esp_account_id, sr.esp_internal_id
            HAVING
              `delivers_diff` < -.075
              || `opens_diff`  < -.075
              || `clicks_diff` < -.075");
    }

    public function pullEspAccount($espAccounts, $date) {
        $date .= ' 00:00:00';
        return DB::table('mt2_reports.email_actions AS ea')
            ->join('mt2_data.emails AS e', 'ea.email_id', '=', 'e.id')
            ->whereIn('esp_account_id', $espAccounts)
            ->whereIn('ea.action_id', [1,2])
            ->where('ea.created_at', '>=', $date)
            ->select('email_address', 'email_id')
            ->distinct()
            ->get();
    }
}
