<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\Cake\CakeConversion;
use App\Services\API\CakeConversionApi;
use Carbon\Carbon;
use DB;

class CakeConversionRepo {
    protected $model;

    public function __construct ( CakeConversion $model ) {
        $this->model = $model; 
    }

    public function getByDate ( $dateRange = null ) {
        if ( is_null( $dateRange ) ) {
            $dateRange = [ "start" => Carbon::today()->startOfDay()->toDateTimeString() , "end" => Carbon::today()->endOfDay()->ToDateTimeString() ];
        }

        return $this->model->select( "email_id" , "s1 as deploy_id" , "conversion_date as date" , "received_usa as revenue" )->whereBetween( "conversion_date" , [ $dateRange[ "start" ] , $dateRange[ "end" ] ] )->get();
    }

    public function getByDeployEmailDate ( $deployId , $emailId , $date  ) {
        $records =  DB::connection( 'reporting_data' )->select( "
            SELECT
                COUNT( * ) AS `conversions` ,
                SUM( price_received ) AS `revenue`
            FROM  
                cake_conversions
            WHERE
                s1 = :deployId
                AND email_id = :emailId
                AND conversion_date between :start and :end
        " , [
            ":deployId" => $deployId ,
            ":emailId" => $emailId ,
            ":start" => Carbon::parse( $date )->startOfDay()->toDateTimeString() ,
            ":end" => Carbon::parse( $date )->endOfDay()->toDateTimeString()
        ] );

        $result = [
            'conversions' => $records[ 0 ]->conversions ,
            'revenue' => ( is_null( $records[ 0 ]->revenue ) ? 0.00 : $records[ 0 ]->revenue )
        ];

        return $result;
    }

    public function insertOrUpdate ( $valueString ) {
        DB::connection( 'reporting_data' )->statement( "
            INSERT INTO
                cake_conversions ( email_id , s1 , s2 , s3 , s4 , s5 , click_id , conversion_date , conversion_id , is_click_conversion , request_session_id , affiliate_id , offer_id , advertiser_id , campaign_id , creative_id , received_raw , received_usa , paid_raw , paid_usa , paid_currency_id , received_currency_id , conversion_rate , ip , created_at , updated_at )    
            VALUES
                {$valueString}
            ON DUPLICATE KEY UPDATE
                email_id = email_id ,
                s1 = s1 ,
                s2 = s2 , 
                s3 = s3 , 
                s4 = s4 ,
                s5 = s5 ,
                click_id = click_id ,
                conversion_date = conversion_date ,
                conversion_id = conversion_id ,
                is_click_conversion = is_click_conversion ,
                request_session_id = request_session_id ,
                affiliate_id = affiliate_id ,
                offer_id = offer_id ,
                advertiser_id = advertiser_id ,
                campaign_id = campaign_id ,
                creative_id = creative_id ,
                received_raw = VALUES( received_raw ) ,
                received_usa = VALUES( received_usa ),
                paid_raw = VALUES( paid_raw ) ,
                paid_usa = VALUES( paid_usa ),
                paid_currency_id = VALUES( paid_currency_id ) ,
                received_currency_id = VALUES( received_currency_id ) ,
                conversion_rate = conversion_rate ,
                ip = VALUES( ip ) ,
                created_at = created_at ,
                updated_at = NOW()
            " );
    }

    public function getConversionsByEmailId($dateRange = null) {
        if ( is_null( $dateRange ) ) {
            $dateRange = [ "start" => Carbon::today()->subDays(5)->toDateTimeString() , "end" => Carbon::today()->endOfDay()->ToDateTimeString() ];
        }

        return $this->model
                    ->select( "email_id" , "s1 as deploy_id", DB::raw('DATE(conversion_date) as date'), DB::raw('COUNT(*) as conversions') )
                    ->whereBetween( "conversion_date" , [ $dateRange[ "start" ] , $dateRange[ "end" ] ] )
                    ->groupBy('email_id', 'deploy_id', 'date');
    }
}
