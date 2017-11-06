<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use App\Models\CpmReportingListProfile;

class CpmListProfileReportRepo {
    protected $model;

    public function __construct ( CpmReportingListProfile $model) {
        $this->model = $model;
    }

    public function getCurrentMonthsPricings () {
        return \DB::select( "SELECT
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
            d.send_date BETWEEN DATE( DATE_FORMAT( NOW() ,'%Y%m01') ) AND LAST_DAY( NOW() )
            AND 
            cos.start_date >= DATE_SUB( now() , INTERVAL 4 MONTH )
        GROUP BY
            cos.offer_id ,
            cos.cake_offer_id ,
            d.id ,
            cos.amount ,
            cos.start_date ,
            cos.end_date" );
    }

    public function getCountsForDeploy ( $deployId ) {
        return \DB::select( "SELECT
            count( * ) AS `scount`,
            cds.feed_id AS `feed_id`
        FROM
            `mt2_reports`.`cpm_deploy_snapshots` cds
            LEFT JOIN `suppression`.`suppression_global_orange` s ON( cds.email_address = s.email_address AND suppress_datetime >= DATE_SUB(now(), INTERVAL 4 MONTH) )
        WHERE
            s.id IS NULL
            AND cds.deploy_id = {$deployId} 
        GROUP BY
            cds.feed_id");
    }

    public function clearForCakeOfferId ( $cakeOfferId ) {
        return $this->model->where( 'cake_offer_id' , $cakeOfferId )->delete();
    }

    public function saveReport ( $records ) {
        foreach ( $records as $current ) {
            $this->model->updateOrCreate(
                [ 'feed_id' => $current[ 'feed_id' ] , 'cake_offer_id' => $current[ 'cake_offer_id' ] ] ,
                $current
            );
        }
    }
}
