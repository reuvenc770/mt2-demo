<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\AttributionFeedReport;
use App\Models\AttributionLevel;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    public function getReportData ( Request $request ) {
        $modelId = $request->input( 'modelId' );
        $startDate = $request->input( 'startDate' );
        $endDate = $request->input( 'endDate' );

        $db = config('database.connections.mysql.database');
        $attrDb = config( 'database.connections.attribution.database' );
        $tableName = AttributionFeedReport::LIVE_TABLE_NAME;
        $modelTableName = AttributionFeedReport::BASE_TABLE_NAME . $modelId;
        $levelTableName = AttributionLevel::LIVE_TABLE_NAME;
        $modelLevelTableName = AttributionLevel::BASE_TABLE_NAME . $modelId;

        return \DB::select( "
        SELECT
            c.name as `clientName` ,
            f.name as `feedName` ,
            afr.uniques ,
            al.level as `liveLevel` ,
            alm.level as `modelLevel` ,
            SUM( afr.cpa_revenue ) as `liveCpaRevenue` ,
            SUM( afrm.cpa_revenue ) as `modelCpaRevenue` ,
            SUM( afr.cpa_revshare ) as `liveCpaRevshare` ,
            SUM( afrm.cpa_revshare ) as `modelCpaRevshare` ,
            SUM( afr.cpc_revenue ) as `liveCpcRevenue` ,
            SUM( afrm.cpc_revenue ) as `modelCpcRevenue` ,
            SUM( afr.cpc_revshare ) as `liveCpcRevshare` ,
            SUM( afrm.cpc_revshare ) as `modelCpcRevshare` ,
            SUM( afr.cpm_revenue ) as `liveCpmRevenue` ,
            SUM( afrm.cpm_revenue ) as `modelCpmRevenue` ,
            SUM( afr.cpm_revshare ) as `liveCpmRevshare` ,
            SUM( afrm.cpm_revshare ) as `modelCpmRevshare`
        FROM
            {$attrDb}.{$tableName} afr
            LEFT JOIN {$db}.feeds f ON( afr.feed_id = f.id )
            LEFT JOIN {$db}.clients c ON( f.client_id = c.id )
            LEFT JOIN {$attrDb}.{$levelTableName} al ON( afr.feed_id = al.feed_id )
            LEFT JOIN {$attrDb}.{$modelLevelTableName} alm ON( afr.feed_id = alm.feed_id )
            LEFT JOIN {$attrDb}.{$modelTableName} afrm ON( afr.feed_id = afrm.feed_id )
        WHERE
            afr.date BETWEEN '{$startDate}' AND '{$endDate}' 
        GROUP BY
            afr.feed_id
        " );
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
