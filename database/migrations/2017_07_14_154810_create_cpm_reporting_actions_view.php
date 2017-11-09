<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpmReportingActionsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $reportDb = config( 'database.connections.reporting_data.database' );

        DB::statement( "CREATE VIEW `{$reportDb}`.`cpm_reporting_actions` ( month , feed_id , cake_offer_id , payout , delivered , rev ) AS
SELECT
    DATE_FORMAT(NOW() ,'%Y-%m') AS `month` ,
    count.feed_id ,
    count.cake_offer_id ,
    count.amount AS `payout` ,
    count.scount AS `delivered`,
    count.scount / 1000 * count.amount AS `rev`
FROM
    ( SELECT
        count( * ) AS `scount`,
        efa.feed_id ,
        o.cake_offer_id ,
        o.amount
    FROM
        ( SELECT
            cos.offer_id AS `mt1_offer_id` ,
            cos.cake_offer_id ,
            d.id AS `deploy_id` ,
            cos.amount
        FROM
            `cpm_offer_schedules` cos 
            INNER JOIN `deploys` d ON( cos.offer_id = d.offer_id )
            INNER JOIN `offer_payouts` op ON( op.offer_id = d.offer_id )
        WHERE
            d.send_date BETWEEN DATE( DATE_FORMAT( NOW() ,'%Y%m01') ) AND LAST_DAY( NOW() )
            AND cos.start_date <= NOW()
            AND cos.end_date >= NOW()
        GROUP BY
            cos.offer_id , cos.cake_offer_id , d.id , cos.amount ) o
        INNER JOIN `mt2_reports`.`email_actions` ea ON( o.deploy_id = ea.deploy_id )
        INNER JOIN `attribution`.`email_feed_assignments` efa ON( ea.email_id = efa.email_id )
    WHERE
        ea.action_id = 4
    GROUP BY
        efa.feed_id ,
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
        $reportDb = config( 'database.connections.reporting_data.database' );

        DB::statement( "DROP VIEW {$reportDb}.cpm_reporting_actions" );
    }
}
