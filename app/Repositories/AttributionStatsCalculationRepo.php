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

    public function getAllOfficialDeployStats($date) {
        // get official stats for a start date, grouped by deploy
        return DB::connection('attribution')->statement("SELECT
            deploy_id,
            SUM(delivered) AS delivered,
            SUM(opens) AS opens,
            SUM(clicks) AS clicks,
            SUM(conversions) AS conversions,
            SUM(bounces) AS bounces,
            SUM(unsubs) AS unsubs,
            SUM(rev) AS rev,
            SUM(cost) AS cost,
            ROUND(SUM(cost) / SUM(delivered), 3) AS ecpm
          FROM
            attribution_reports
          WHERE
            date >= ?
          GROUP BY
            deploy_id", [$date])->get();
    }

    public function getDeployOfficialStats($date, $deployId) {
        // get official stats for a particular deploy and start date
        return DB::connection('attribution')->statement("SELECT
          deploy_id,
          SUM(delivered) AS delivered,
          SUM(opens) AS opens,
          SUM(clicks) AS clicks,
          SUM(conversions) AS conversions,
          SUM(bounces) AS bounces,
          SUM(unsubs) AS unsubs,
          SUM(rev) AS rev,
          SUM(cost) AS cost,
          ROUND(SUM(cost) / SUM(delivered), 3) AS ecpm
        FROM
          attribution_reports
        WHERE
          date >= ?
          AND
          deploy_id = ?", [$date, $deployId])->get();
    }

    public function getDeployModelStats($date, $deployId, $modelId) {
        // get all stats for a particular model, date, and deploy

        $modelTable = "attribution_transient_records_model_" . $modelId;
        
        return DB::connection('attribution')->statement("SELECT
              aar.deploy_id,
              SUM(aar.delivered) + SUM(m.delivered) AS delivered,
              SUM(aar.opens) + SUM(m.opens) AS opens,
              SUM(aar.clicks) + SUM(m.clicks) AS clicks,
              SUM(aar.conversions) + SUM(m.conversions) AS conversions,
              SUM(aar.bounces) + SUM(m.bounces) AS bounces,
              SUM(aar.unsubs) + SUM(m.unsubs) AS unsubs,
              SUM(aar.rev) + SUM(m.rev) AS rev,
              SUM(aar.cost) + SUM(m.cost) AS cost,
              ROUND( (SUM(aar.cost) + SUM(m.cost)) / (SUM(aar.delivered) + SUM(m.delivered)), 3) AS ecpm
            FROM
              (SELECT
                  deploy_id,
                  SUM(delivered) AS delivered,
                  SUM(opens) AS opens,
                  SUM(clicks) AS clicks,
                  SUM(conversions) AS conversions,
                  SUM(bounces) AS bounces,
                  SUM(unsubs) AS unsubs,
                  SUM(rev) AS rev,
                  SUM(cost) AS cost,
                  ROUND(SUM(cost) / SUM(delivered), 3) AS ecpm
                FROM
                  attribution_assigned_records
                WHERE
                  date >= ?
                  AND
                  deploy_id = ?) aar
                LEFT JOIN (SELECT
                  deploy_id,
                  SUM(delivered) AS delivered,
                  SUM(opens) AS opens,
                  SUM(clicks) AS clicks,
                  SUM(conversions) AS conversions,
                  SUM(bounces) AS bounces,
                  SUM(unsubs) AS unsubs,
                  SUM(rev) AS rev,
                  SUM(cost) AS cost,
                  ROUND(SUM(cost) / SUM(delivered), 3) AS ecpm
                FROM
                  $modelTable
                WHERE
                  date >= ?
                  AND
                  deploy_id = ?) m ON aar.deploy_id = m.deploy_id", 
            [$date, $deployId, $date, $deployId])->get();
    }

    public function getModelDeployStats($date, $modelId) {
        // get all stats for a particular model and start date, grouped by deploy

        $modelTable = "attribution_transient_records_model_" . $modelId;

        return DB::connection('attribution')->statement("SELECT
              aar.deploy_id,
              SUM(aar.delivered) + SUM(m.delivered) AS delivered,
              SUM(aar.opens) + SUM(m.opens) AS opens,
              SUM(aar.clicks) + SUM(m.clicks) AS clicks,
              SUM(aar.conversions) + SUM(m.conversions) AS conversions,
              SUM(aar.bounces) + SUM(m.bounces) AS bounces,
              SUM(aar.unsubs) + SUM(m.unsubs) AS unsubs,
              SUM(aar.rev) + SUM(m.rev) AS rev,
              SUM(aar.cost) + SUM(m.cost) AS cost,
              ROUND( (SUM(aar.cost) + SUM(m.cost)) / (SUM(aar.delivered) + SUM(m.delivered)), 3) AS ecpm
            FROM
              (SELECT
                  deploy_id,
                  SUM(delivered) AS delivered,
                  SUM(opens) AS opens,
                  SUM(clicks) AS clicks,
                  SUM(conversions) AS conversions,
                  SUM(bounces) AS bounces,
                  SUM(unsubs) AS unsubs,
                  SUM(rev) AS rev,
                  SUM(cost) AS cost,
                  ROUND(SUM(cost) / SUM(delivered), 3) AS ecpm
                FROM
                  attribution_assigned_records
                WHERE
                  date >= ?
                GROUP BY
                  deploy_id) aar
                LEFT JOIN (SELECT
                  deploy_id,
                  SUM(delivered) AS delivered,
                  SUM(opens) AS opens,
                  SUM(clicks) AS clicks,
                  SUM(conversions) AS conversions,
                  SUM(bounces) AS bounces,
                  SUM(unsubs) AS unsubs,
                  SUM(rev) AS rev,
                  SUM(cost) AS cost,
                  ROUND(SUM(cost) / SUM(delivered), 3) AS ecpm
                FROM
                  $modelTable
                WHERE
                  date >= ?
                GROUP BY
                  deploy_id) m ON aar.deploy_id = m.deploy_id", [$date, $date])->get();
    }

    public function getAllOfficialFeedStats($date) {
        // get official stats for a start date, grouped by feed
        return DB::connection('attribution')->statement("SELECT
            client_id,
            SUM(delivered) AS delivered,
            SUM(opens) AS opens,
            SUM(clicks) AS clicks,
            SUM(conversions) AS conversions,
            SUM(bounces) AS bounces,
            SUM(unsubs) AS unsubs,
            SUM(rev) AS rev,
            SUM(cost) AS cost,
            ROUND(SUM(cost) / SUM(delivered), 3) AS ecpm
          FROM
            attribution_reports
          WHERE
            date >= ?
          GROUP BY
            client_id", [$date])->get();
    }

    public function getFeedOfficialStats($date, $feedId) {
        // get official stats for a particular feed and start date
        return DB::connection('attribution')->statement("SELECT
          client_id,
          SUM(delivered) AS delivered,
          SUM(opens) AS opens,
          SUM(clicks) AS clicks,
          SUM(conversions) AS conversions,
          SUM(bounces) AS bounces,
          SUM(unsubs) AS unsubs,
          SUM(rev) AS rev,
          SUM(cost) AS cost,
          ROUND(SUM(cost) / SUM(delivered), 3) AS ecpm
        FROM
          attribution_reports
        WHERE
          date >= ?
          AND
          client_id = ?", [$date, $feedId])->get();
    }

    public function getFeedModelStats($date, $feedId, $modelId) {
        // get all stats for a particular model, date, and feed
        $modelTable = "attribution_transient_records_model_" . $modelId;
        
        return DB::connection('attribution')->statement("SELECT
              aar.feed_id,
              SUM(aar.delivered) + SUM(m.delivered) AS delivered,
              SUM(aar.opens) + SUM(m.opens) AS opens,
              SUM(aar.clicks) + SUM(m.clicks) AS clicks,
              SUM(aar.conversions) + SUM(m.conversions) AS conversions,
              SUM(aar.bounces) + SUM(m.bounces) AS bounces,
              SUM(aar.unsubs) + SUM(m.unsubs) AS unsubs,
              SUM(aar.rev) + SUM(m.rev) AS rev,
              SUM(aar.cost) + SUM(m.cost) AS cost,
              ROUND( (SUM(aar.cost) + SUM(m.cost)) / (SUM(aar.delivered) + SUM(m.delivered)), 3) AS ecpm
            FROM
              (SELECT
                  feed_id,
                  SUM(delivered) AS delivered,
                  SUM(opens) AS opens,
                  SUM(clicks) AS clicks,
                  SUM(conversions) AS conversions,
                  SUM(bounces) AS bounces,
                  SUM(unsubs) AS unsubs,
                  SUM(rev) AS rev,
                  SUM(cost) AS cost,
                  ROUND(SUM(cost) / SUM(delivered), 3) AS ecpm
                FROM
                  attribution_assigned_records
                WHERE
                  date >= ?
                  AND
                  feed_id = ?) aar
                LEFT JOIN (SELECT
                  feed_id,
                  SUM(delivered) AS delivered,
                  SUM(opens) AS opens,
                  SUM(clicks) AS clicks,
                  SUM(conversions) AS conversions,
                  SUM(bounces) AS bounces,
                  SUM(unsubs) AS unsubs,
                  SUM(rev) AS rev,
                  SUM(cost) AS cost,
                  ROUND(SUM(cost) / SUM(delivered), 3) AS ecpm
                FROM
                  $modelTable
                WHERE
                  date >= ?
                  AND
                  client_id = ?) m ON aar.feed_id = m.feed_id" [$date, $feedId, $date, $feedId])->get();
    }

    public function getModelFeedStats($date, $modelId) {
        // get all stats for a particular model and start date, grouped by feed
        $modelTable = "attribution_transient_records_model_" . $modelId;

        return DB::connection('attribution')->statement("SELECT
              aar.client_id,
              SUM(aar.delivered) + SUM(m.delivered) AS delivered,
              SUM(aar.opens) + SUM(m.opens) AS opens,
              SUM(aar.clicks) + SUM(m.clicks) AS clicks,
              SUM(aar.conversions) + SUM(m.conversions) AS conversions,
              SUM(aar.bounces) + SUM(m.bounces) AS bounces,
              SUM(aar.unsubs) + SUM(m.unsubs) AS unsubs,
              SUM(aar.rev) + SUM(m.rev) AS rev,
              SUM(aar.cost) + SUM(m.cost) AS cost,
              ROUND( (SUM(aar.cost) + SUM(m.cost)) / (SUM(aar.delivered) + SUM(m.delivered)), 3) AS ecpm
            FROM
              (SELECT
                  client_id,
                  SUM(delivered) AS delivered,
                  SUM(opens) AS opens,
                  SUM(clicks) AS clicks,
                  SUM(conversions) AS conversions,
                  SUM(bounces) AS bounces,
                  SUM(unsubs) AS unsubs,
                  SUM(rev) AS rev,
                  SUM(cost) AS cost,
                  ROUND(SUM(cost) / SUM(delivered), 3) AS ecpm
                FROM
                  attribution_assigned_records
                WHERE
                  date >= ?
                GROUP BY
                  client_id) aar
                LEFT JOIN (SELECT
                  client_id,
                  SUM(delivered) AS delivered,
                  SUM(opens) AS opens,
                  SUM(clicks) AS clicks,
                  SUM(conversions) AS conversions,
                  SUM(bounces) AS bounces,
                  SUM(unsubs) AS unsubs,
                  SUM(rev) AS rev,
                  SUM(cost) AS cost,
                  ROUND(SUM(cost) / SUM(delivered), 3) AS ecpm
                FROM
                  $modelTable
                WHERE
                  date >= ?
                GROUP BY
                  client_id) m ON aar.client_id = m.client_id", [$date, $date])->get();
    }
}