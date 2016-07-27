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
}
