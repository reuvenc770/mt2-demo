<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\Attribution;

use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\AttributionFeedReport;
use Carbon\Carbon;

class FeedReportRepo {
    protected $model;

    public function __construct ( AttributionFeedReport $model ) {
        $this->model = $model;
    }

    public function getAggregateForIdAndMonth ( $clientId , $date ) { 
        $dateRange = [ 'start' => Carbon::parse( $date )->startOfMonth()->toDateString() , 'end' => Carbon::parse( $date )->endOfMonth()->toDateString() ];

        return $this->model
            ->select( DB::raw( "
                SUM( revenue ) as revenue ,
                SUM( mt1_uniques ) as mt1_uniques ,
                SUM( mt2_uniques ) as mt2_uniques
            " ) ) 
            ->where( 'client_id' , $clientId )
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
                attribution_feed_reports ( client_id , revenue , mt1_uniques , mt2_uniques , date , created_at , updated_at )
            VALUES
                {$valuesSqlString}
            ON DUPLICATE KEY UPDATE
                client_id = client_id ,
                revenue = VALUES( revenue ) ,
                mt1_uniques = VALUES( mt1_uniques ) ,
                mt2_uniques = VALUES( mt2_uniques ) ,
                date = date ,
                created_at = created_at ,
                updated_at = NOW()
        " );
    }

    static public function generateTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->create( AttributionFeedReport::BASE_TABLE_NAME . $modelId, function (Blueprint $table) {
            $table->increments('id');
            $table->integer( 'feed_id' )->unsigned();
            $table->decimal( 'revenue' , 11 , 3 )->unsigned()->default( 0.00 );
            $table->integer( 'mt1_uniques' )->unsigned()->default( 0 );
            $table->integer( 'mt2_uniques' )->unsigned()->default( 0 );
            $table->date( 'date' );
            $table->timestamps();

            $table->unique( [ 'feed_id' , 'date' ] );
        });
    }

    static public function dropTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->drop( AttributionFeedReport::BASE_TABLE_NAME . $modelId );
    }
}
