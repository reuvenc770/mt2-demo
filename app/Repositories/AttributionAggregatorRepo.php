<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Repositories;

use App\Models\AttributionFeedReport;

class AttributionAggregatorRepo {
    const ASSIGNMENTS_BASE_TABLE_NAME = 'email_feed_assignments_model_';

    protected $model;

    public function __construct ( AttributionFeedReport $model ){
        $this->model = $model;
    }

    public function standardRun ( $dateRange , $modelId = null ) {
        $records = $this->getStandardRevenue ( $dateRange  , $modelId);
    }

    public function cpmRun ( $offerId , $dateRange , $modelId = null ) {
        $records = $this->getCpmRevenue ( $offerId , $dateRange , $modelId );
    }

    public function getStandardRevenue ( $dateRange , $modelId = null ) {
        $db = config('database.connections.mysql.database');
        $attrDb = config( 'database.connections.attribution.database' );
        $reportDb = config( 'database.connections.reporting_data.database' );

        $reportTable = ( !is_null( $modelId ) ? AttributionFeedReport::BASE_TABLE_NAME . $modelId : AttributionFeedReport::LIVE_TABLE_NAME);
        $assignmentTable = ( !is_null( $modelId ) ? self::ASSIGNMENTS_BASE_TABLE_NAME . $modelId : self::ASSIGNMENTS_BASE_TABLE_NAME );

        return \DB::select( "INSERT INTO
            {$attrDb}.{$reportTable} ( date , feed_id , cpc_revenue , cpc_revshare , cpa_revenue , cpa_revshare , uniques , updated_at )
            SELECT
                rev.date ,
                rev.feed_id ,
                rev.cpa_rev ,
                rev.cpa_rev * COALESCE( f.revshare , 0 ) ,
                rev.cpc_rev ,
                rev.cpc_rev * COALESCE( f.revshare , 0 ) ,
                COALESCE( u.uniques , 0 ) AS `uniques` ,
                NOW()
            FROM
                (   SELECT
                        COALESCE( efa.feed_id , 0 ) AS `feed_id` ,
                        SUM( IFNULL( IF( cc.is_click_conversion = 0 , cc.received_usa , 0 ) , 0 ) ) AS `cpa_rev` ,
                        SUM( IFNULL( IF( cc.is_click_conversion = 1 , cc.received_usa , 0 ) , 0 ) ) AS `cpc_rev` ,
                        DATE( conversion_date ) AS `date`
                    FROM
                        {$reportDb}.cake_conversions cc
                        LEFT JOIN {$attrDb}.{$assignmentTable} efa ON( cc.email_id = efa.email_id )
                    WHERE
                        cc.conversion_date BETWEEN '{$dateRange[ 'start' ]}' AND '{$dateRange[ 'end' ]}' 
                    GROUP BY
                        efa.feed_id ,
                        `date`
                ) as rev
                LEFT JOIN (
                    SELECT
                        SUM( IFNULL( fdeb.unique_emails , 0 ) ) AS `uniques` ,
                        fdeb.feed_id ,
                        fdeb.date
                    FROM
                        {$db}.feed_date_email_breakdowns fdeb
                    WHERE
                        fdeb.date BETWEEN '{$dateRange[ 'start' ]}' AND '{$dateRange[ 'end' ]}'
                    GROUP BY
                        fdeb.feed_id ,
                        fdeb.date
                ) u ON( u.feed_id = rev.feed_id AND u.date = rev.date )
                LEFT JOIN {$db}.feeds f ON( f.id = rev.feed_id )
            ON DUPLICATE KEY UPDATE
                cpc_revenue = VALUES( cpc_revenue ) ,
                cpc_revshare = VALUES( cpc_revshare ) ,
                cpa_revenue = VALUES( cpa_revenue ) ,
                cpa_revshare = VALUES( cpa_revshare ) ,
                uniques = VALUES( uniques ) ,
                updated_at = NOW()" );
    }

    public function getCpmRevenue ( $offerId , $dateRange , $modelId = null ) {
        $db = config('database.connections.mysql.database');
        $attrDb = config( 'database.connections.attribution.database' );
        $reportDb = config( 'database.connections.reporting_data.database' );

        $reportTable = ( !is_null( $modelId ) ? AttributionFeedReport::BASE_TABLE_NAME . $modelId : AttributionFeedReport::LIVE_TABLE_NAME);
        $assignmentTable = ( !is_null( $modelId ) ? self::ASSIGNMENTS_BASE_TABLE_NAME . $modelId : self::ASSIGNMENTS_BASE_TABLE_NAME );

        return \DB::select( "INSERT INTO
            {$attrDb}.{$reportTable} ( date , feed_id , cpm_revenue , cpm_revshare , updated_at )   
        SELECT
            aggr.`date`,
            aggr.`feed_id` ,
            aggr.`count` / 1000 * op.amount as `cpm_revenue` ,
            aggr.`count` / 1000 * op.amount * COALESCE( f.revshare , 0 ) as `cpm_revshare` ,
            NOW()
        FROM
            ( SELECT
                DATE( ea.datetime ) as `date`,
                d.offer_id,
                efa.feed_id ,
                count( * ) AS `count`
            FROM
                {$reportDb}.email_actions ea
                INNER JOIN {$db}.deploys d ON( d.id = ea.deploy_id )
                LEFT JOIN {$attrDb}.{$assignmentTable} efa ON( ea.email_id = efa.email_id )
            WHERE
                ea.datetime BETWEEN '{$dateRange[ 'start' ]}' AND '{$dateRange[ 'end' ]}'
                AND ea.action_id = 4
                AND d.offer_id = '{$offerId}' 
            GROUP BY
                `date` ,
                efa.feed_id
            ) aggr
            LEFT JOIN {$db}.offer_payouts op ON( aggr.offer_id = op.offer_id )
            LEFT JOIN {$db}.feeds f ON( aggr.`feed_id` = f.id )
        ON DUPLICATE KEY UPDATE
            cpm_revenue = VALUES( cpm_revenue ) ,
            updated_at = NOW()" );
    }
}
