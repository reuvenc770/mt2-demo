<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\Cake\CakeConversion;
use Carbon\Carbon;

class CakeConversionRepo {
    protected $model;

    public function __construct ( CakeConversion $model ) {
        $this->model = $model; 
    }

    public function getByDate ( $dateRange = null ) {
        if ( is_null( $dateRange ) ) {
            $dateRange = [ "start" => Carbon::today()->startOfDay()->toDateTimeString() , "end" => Carbon::today()->endOfDay()->ToDateTimeString() ];
        }

        return $this->model->select( "email_id" , "s1 as deploy_id" , "conversion_date" , "price_received as revenue" )->whereBetween( "conversion_date" , [ $dateRange[ "start" ] , $dateRange[ "end" ] ] )->get();
    }

    public function getByDeployEmailDate ( $deployId , $emailId , $date  ) {
        $dateObj = Carbon::parse( $date );

        $dateRange = [
            "start" => $dateObj->startOfDay()->toDateTimeString() ,
            "end" => $dateObj->endOfDay()->toDateTimeString() 
        ];

        return DB::connection( 'reporting_data' )->select( "
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
            ":start" => $dateRange[ 'start' ] ,
            ":end" => $dateRange[ 'end' ] 
        ] )->pluck( 'conversions' , 'revenue')->pop();
    }
}
