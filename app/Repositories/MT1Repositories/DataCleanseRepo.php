<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\MT1Repositories;

use DB;
use Log;
use Carbon\Carbon;

class DataCleanseRepo {
    public function __construct () {}

    public function getType () {
        return 'datacleanse';
    }

    public function getAll () {
        try{
            return DB::connection('mt1mail')
                ->table('DataExport')
                ->select( DB::raw(
                    'exportID AS `id` ,
                    fileName AS `name` ,
                    IFNULL( CONCAT( lastUpdated , " " , lastUpdatedTime ) , "" ) AS `lastUpdated` , 
                    recordCount AS `count`' ) )
                ->where( [
                    [ 'exportType' , 'Cleanse' ] ,
                    [ 'status' , 'Active' ] ,
                    [ 'lastUpdated' , '>' , Carbon::now()->subMonths( 2 ) ]
                ] )
                ->orderBy( 'fileName' , 'ASC' )
                ->get();
        } catch (\Exception $e){
            Log::error("DataCleanseRepo error:: ".$e->getMessage());
        }
    }
}
