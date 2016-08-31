<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\Attribution;

use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\AttributionClientReport;
use Carbon\Carbon;

class ClientReportRepo {
    protected $clientReport;

    public function __construct ( AttributionClientReport $clientReport ) {
        $this->clientReport = $clientReport;
    }

    public function switchToLiveTable () {
        $this->clientReport->switchToLiveTable();
    }

    public function setModelId ( $modelId ) {
        $this->clientReport->setModelId( $modelId );
    }

    public function getAggregateForIdAndMonth ( $clientId , $date ) {
        $dateRange = [ 'start' => Carbon::parse( $date )->startOfMonth()->toDateString() , 'end' => Carbon::parse( $date )->endOfMonth()->toDateString() ];

        return $this->clientReport
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

    public function getClientsForDateRange ( $startDate , $endDate ) {
        return $this->clientReport
                    ->select( 'client_stats_grouping_id as id' )
                    ->distinct()
                    ->whereBetween( 'date' , [ $startDate , $endDate ] )
                    ->get();
    }

    public function getClientsFromLastThreeMonths () {
        return $this->getClientsForDateRange( Carbon::today()->subMonths( 2 )->toDateString() , Carbon::today()->endOfMonth()->toDateString() );
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

    static public function generateTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->create( AttributionClientReport::BASE_TABLE_NAME . $modelId , function (Blueprint $table) {
            $table->increments('id');
            $table->integer( 'client_stats_grouping_id' )->unsigned();
            $table->decimal( 'standard_revenue' , 11 , 3 )->unsigned()->default( 0.00 );
            $table->decimal( 'cpm_revenue' , 11 , 3 )->unsigned()->default( 0.00 );
            $table->integer( 'mt1_uniques' )->unsigned()->default( 0 );
            $table->integer( 'mt2_uniques' )->unsigned()->default( 0 );
            $table->date( 'date' );
            $table->timestamps();
        });
    }

    static public function dropTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->drop( AttributionClientReport::BASE_TABLE_NAME . $modelId );
    }
}
