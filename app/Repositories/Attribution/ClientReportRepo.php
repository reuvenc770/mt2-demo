<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\Attribution;

use DB;
use App\Models\AttributionClientReport;
use Carbon\Carbon;

class ClientReportRepo {
    protected $model;

    public function __construct ( AttributionClientReport $model ) {
        $this->model = $model;
    }

    public function getAggregateForIdAndMonth ( $clientId , $date ) {
        $dateRange = [ 'start' => Carbon::parse( $date )->startOfMonth()->toDateString() , 'end' => Carbon::parse( $date )->endOfMonth()->toDateString() ];

        return $this->model
            ->select( DB::raw( "
                SUM( standard_revenue ) as standard_revenue ,
                SUM( cpm_revenue ) as cpm_revenue ,
                SUM( mt1_uniques ) as mt1_uniques ,
                SUM( mt2_uniques ) as mt2_uniques
            " ) )
            ->where( 'client_stats_grouping_id' , $clientId )
            ->whereBetween( 'date' , [ $dateRange[ 'start' ] , $dateRange[ 'end' ] ] )
            ->get()
            ->pop();
    }

    public function getClientsFromLastThreeMonths () {
        return $this->model
                    ->select( 'client_stats_grouping_id as id' )
                    ->distinct()
                    ->whereBetween( 'date' , [ Carbon::today()->subMonths( 2 )->toDateString() , Carbon::today()->endOfMonth()->toDateString() ] )
                    ->get();
    }

    public function runInsertQuery ( $valuesSqlString ) {
        DB::connection( 'attribution' )->insert( "
            INSERT INTO
                attribution_client_reports ( client_stats_grouping_id , standard_revenue , cpm_revenue , mt1_uniques , mt2_uniques , date , created_at , updated_at )
            VALUES
                {$valuesSqlString}
            ON DUPLICATE KEY UPDATE
                client_stats_grouping_id = client_stats_grouping_id ,
                standard_revenue = VALUES( standard_revenue ) ,
                cpm_revenue = VALUES( cpm_revenue ) ,
                mt1_uniques = VALUES( mt1_uniques ) ,
                mt2_uniques = VALUES( mt2_uniques ) ,
                date = date ,
                created_at = created_at ,
                updated_at = NOW()
        " );
    }
}
