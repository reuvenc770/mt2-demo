<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpmReportingListprofileView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement( "CREATE VIEW `mt2_reports`.`cpm_reporting_listprofile` ( feed_id , cake_offer_id , payout , delivered , rev ) AS
SELECT
    count.feed_id ,
    count.cake_offer_id ,
    count.amount AS `payout` ,
    count.scount AS `delivered`,
    count.scount / 1000 * count.amount AS `rev`
FROM
    ( SELECT
        count( * ) AS `scount`,
        cds.feed_id ,
        o.cake_offer_id ,
        o.amount
    FROM
        ( SELECT
            cos.offer_id AS `mt1_offer_id` ,
            cos.cake_offer_id ,
            d.id AS `deploy_id` ,
            cos.amount ,
            cos.start_date AS `io_start_date`, 
            cos.end_date AS `io_end_date`
        FROM
            `cpm_offer_schedules` cos 
            INNER JOIN `deploys` d ON( cos.offer_id = d.offer_id )
        WHERE
            d.send_date >= DATE_SUB( NOW() , INTERVAL 4 MONTH )
            AND 
            cos.start_date >= DATE_SUB( NOW() , INTERVAL 4 MONTH )
        GROUP BY
            cos.offer_id ,
            cos.cake_offer_id ,
            d.id ,
            cos.amount ,
            cos.start_date ,
            cos.end_date ) o
        INNER JOIN `mt2_reports`.`cpm_deploy_snapshots` cds ON( o.deploy_id = cds.deploy_id )
        LEFT JOIN `suppressions` s ON( cds.email_address = s.email_address AND date >= DATE_SUB(now(), INTERVAL 4 MONTH) )
    WHERE
        s.id IS NULL
    GROUP BY
        cds.feed_id ,
        o.cake_offer_id ,
        o.amount ) count;" );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement( 'DROP VIEW mt2_reports.cpm_reporting_listprofile' );
    }
}
