<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *  Repository to handle the various models
 *  This is an "abstract" repo that potentially handles multiple models/tables,
 *  as opposed to a particular entity
 */

class AttributionStatsCalculationRepo {
    
    public function __construct() {}

    public function getAllOfficialStats($date) {
        return DB::connection('attribution')
                    ->table('attribution_reports')
                    ->where('date', '>=', $date)
                    ->get();
    }

    public function getOfficialDeployStats($date, $deployId) {
        // get official stats for a particular deploy and start date
        return DB::connection('attribution')
                    ->table('attribution_reports')
                    ->where('date', '>=', $date)
                    ->where('deploy_id', $deployId)
                    ->get();
    }

    public function getOfficialFeedStats($date, $feedId) {
        // get official stats for a particular feed and start date
        return DB::connection('attribution')
                    ->table('attribution_reports')
                    ->where('date', '>=', $date)
                    ->where('client_id', $feedId)
                    ->get();
    }

    public function getOfficialDeployFeedStats($date, $deployId, $feedId) {
        // get official stats for a particular feed, deploy, and start date
        return DB::connection('attribution')
                    ->table('attribution_reports')
                    ->where('date', '>=', $date)
                    ->where('client_id', $feedId)
                    ->where('deploy_id', $deployId)
                    ->get();
    }

    public function getModelFeedStats($date, $modelId, $feedId){
        // Get model stats for a particular feed

        $modelTable = "attribution_transient_records_model_" . $modelId;

        return DB::connection('attribution')->statement("SELECT
              aar.deploy_id,
              aar.client_id,
              aar.delivered + m.delivered AS delivered,
              aar.opens + m.opens AS opens,
              aar.clicks + m.clicks AS clicks,
              aar.conversions + m.conversions AS conversions,
              aar.bounces + m.bounces AS bounces,
              aar.unsubs + m.unsubs AS unsubs,
              aar.rev + m.rev AS rev,
              aar.cost + m.cost AS cost,
              ROUND( (aar.cost + m.cost) / (aar.delivered + m.delivered) * 1000, 3) AS ecpm
            FROM
              (SELECT
                  *
                FROM
                  attribution_assigned_records
                WHERE
                  date >= ?
                  AND
                  client_id = ?) aar

                LEFT JOIN (SELECT
                  *
                FROM
                  $modelTable
                WHERE
                  date >= ?
                  AND
                  client_id = ?) m ON aar.deploy_id = m.deploy_id AND aar.client_id = m.client_id", 
            [$date, $feedId, $date, $feedId])->get();
    }    

    public function getModelDeployFeedStats($date, $modelId, $deployId, $feedId){
        // get models stats for a particular feed and deploy

        $modelTable = "attribution_transient_records_model_" . $modelId;

        return DB::connection('attribution')->statement("SELECT
              aar.deploy_id,
              aar.client_id,
              aar.delivered + m.delivered AS delivered,
              aar.opens + m.opens AS opens,
              aar.clicks + m.clicks AS clicks,
              aar.conversions + m.conversions AS conversions,
              aar.bounces + m.bounces AS bounces,
              aar.unsubs + m.unsubs AS unsubs,
              aar.rev + m.rev AS rev,
              aar.cost + m.cost AS cost,
              ROUND( (aar.cost + m.cost) / (aar.delivered + m.delivered) * 1000, 3) AS ecpm
            FROM
              (SELECT
                  *
                FROM
                  attribution_assigned_records
                WHERE
                  date >= ?
                  AND
                  deploy_id = ?
                  AND
                  client_id = ?) aar
                LEFT JOIN (SELECT
                  *
                FROM
                  $modelTable
                WHERE
                  date >= ?
                  AND
                  deploy_id = ?
                  AND
                  client_id = ?) m ON aar.deploy_id = m.deploy_id AND aar.client_id = m.client_id", 
            [$date, $deployId, $feedId, $date, $deployId, $feedId])->get();

    }

    public function getModelDeployStats($date, $modelId, $deployId) {
        // get all stats for a particular model, date, and deploy

        $modelTable = "attribution_transient_records_model_" . $modelId;
        
        return DB::connection('attribution')->statement("SELECT
              aar.deploy_id,
              aar.client_id,
              aar.delivered + m.delivered AS delivered,
              aar.opens + m.opens AS opens,
              aar.clicks + m.clicks AS clicks,
              aar.conversions + m.conversions AS conversions,
              aar.bounces + m.bounces AS bounces,
              aar.unsubs + m.unsubs AS unsubs,
              aar.rev + m.rev AS rev,
              aar.cost + m.cost AS cost,
              ROUND( (aar.cost + m.cost) / (aar.delivered + m.delivered) * 1000, 3) AS ecpm
            FROM
              (SELECT
                  *
                FROM
                  attribution_assigned_records
                WHERE
                  date >= ?
                  AND
                  deploy_id = ?) aar
                LEFT JOIN (SELECT
                  *
                FROM
                  $modelTable
                WHERE
                  date >= ?
                  AND
                  deploy_id = ?) m ON aar.deploy_id = m.deploy_id AND aar.client_id = m.client_id", 
            [$date, $deployId, $date, $deployId])->get();
    }

    public function getModelStats($date, $modelId) {
        // get all stats for a particular model and start date

        $modelTable = "attribution_transient_records_model_" . $modelId;

        return DB::connection('attribution')->statement("SELECT
              aar.deploy_id,
              aar.client_id,
              aar.delivered + m.delivered AS delivered,
              aar.opens + m.opens AS opens,
              aar.clicks + m.clicks AS clicks,
              aar.conversions + m.conversions AS conversions,
              aar.bounces + m.bounces AS bounces,
              aar.unsubs + m.unsubs AS unsubs,
              aar.rev + m.rev AS rev,
              aar.cost + m.cost AS cost,
              ROUND( (aar.cost + m.cost) / (aar.delivered + m.delivered) * 1000, 3) AS ecpm
            FROM
              (SELECT
                  *
                FROM
                  attribution_assigned_records
                WHERE
                  date >= ?) aar

                LEFT JOIN (SELECT
                  *
                FROM
                  $modelTable
                WHERE
                  date >= ?) m ON aar.deploy_id = m.deploy_id AND aar.client_id = m.client_id", 
            [$date, $date])->get();
    }

}