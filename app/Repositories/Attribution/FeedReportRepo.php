<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\Attribution;

use DB;
use App\Models\AttributionFeedReport;
use Carbon\Carbon;

class FeedReportRepo {
    protected $model;

    public function __construct ( AttributionFeedReport $model ) {
        $this->model = $model;
    }

    public function getAggregateForIdAndMonth ( $feedId , $date ) { 
        $dateRange = [ 'start' => Carbon::parse( $date )->startOfMonth()->toDateString() , 'end' => Carbon::parse( $date )->endOfMonth()->toDateString() ];

        return $this->model
            ->select( DB::raw( "
                SUM( revenue ) as revenue ,
                SUM( mt1_uniques ) as mt1_uniques ,
                SUM( mt2_uniques ) as mt2_uniques
            " ) ) 
            ->where( 'feed_id' , $feedId )
            ->whereBetween( 'date' , [ $dateRange[ 'start' ] , $dateRange[ 'end' ] ] ) 
            ->get()
            ->pop();
    }   

    public function getByDateRange ( array $dateRange ) {
        return $this->model->whereBetween( 'date' , [ $dateRange[ 'start' ] , $dateRange[ 'end' ] ] )->get();
    }

    public function runInsertQuery ( $valuesSqlString ) {
        DB::connection( 'attribution' )->insert( "
            INSERT INTO
                attribution_feed_reports ( feed_id , revenue , mt1_uniques , mt2_uniques , date , created_at , updated_at )
            VALUES
                {$valuesSqlString}
            ON DUPLICATE KEY UPDATE
                feed_id = feed_id ,
                revenue = VALUES( revenue ) ,
                mt1_uniques = VALUES( mt1_uniques ) ,
                mt2_uniques = VALUES( mt2_uniques ) ,
                date = date ,
                created_at = created_at ,
                updated_at = NOW()
        " );
    }
}
