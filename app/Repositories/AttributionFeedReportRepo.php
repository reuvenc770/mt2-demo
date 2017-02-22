<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\AttributionFeedReport;
use Carbon\Carbon;

class AttributionFeedReportRepo {
    protected $feedReport;

    protected $modelId;

    public function __construct ( AttributionFeedReport $feedReport ) {
        $this->feedReport = $feedReport;
    }

    public function switchToLiveTable () {
        $this->modelId = null;

        $this->feedReport->switchToLiveTable();
    }

    public function setModelId ( $modelId ) {
        $this->modelId = $modelId;

        $this->feedReport->setModelId( $modelId );
    }

    public function getByDateRange ( array $dateRange ) {
        return $this->feedReport->whereBetween( 'date' , [ $dateRange[ 'start' ] , $dateRange[ 'end' ] ] )->get();
    }

    public function getFeedsForDateRange ( $startDate , $endDate ) {
        return $this->feedReport
            ->select( 'feed_id as id' )
            ->distinct()
            ->whereBetween( 'date' , [ $startDate , $endDate ] )
            ->get();
    }

    static public function generateTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->create( AttributionFeedReport::BASE_TABLE_NAME . $modelId , function ( $table ) {
            $table->integer( 'feed_id' );
            $table->decimal( 'cpc_revenue' , 11 , 4 )->unsigned()->default( 0.0000 );
            $table->decimal( 'cpc_revshare' , 11 , 4 )->unsigned()->default( 0.0000 );
            $table->decimal( 'cpa_revenue' , 11 , 4 )->unsigned()->default( 0.0000 );
            $table->decimal( 'cpa_revshare' , 11 , 4 )->unsigned()->default( 0.0000 );
            $table->decimal( 'cpm_revenue' , 11 , 4 )->unsigned()->default( 0.0000 );
            $table->decimal( 'cpm_revshare' , 11 , 4 )->unsigned()->default( 0.0000 );
            $table->integer( 'uniques' )->unsigned()->default( 0.0000 );
            $table->date( 'date' );
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->index( 'feed_id' );
            $table->index( 'date' );
            $table->unique( [ 'feed_id' , 'date' ] , 'feed_date_unique' );
        } );
    }

    static public function dropTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->drop( AttributionFeedReport::BASE_TABLE_NAME . $modelId );
    }
}
