<?php

namespace App\Repositories\MT1Repositories;

use DB;
use Log;

class AdvertiserRepo {
    public function __construct () {}

    public function getAll () {
        try {
            return DB::connection( 'mt1mail' )
                ->table( 'advertiser_info' )
                ->select( 'advertiser_id as id' , 'advertiser_name as name' )
                ->where( [
                    [ 'status' , 'A' ] ,
                    [ 'test_flag' , 'N' ]
                ] )
                ->orderBy( 'advertiser_name' , 'DESC' )
                ->get();
        } catch ( \Exception $e ) {
            Log::error( "AdvertiserRepo Error:: " . $e->getMessage() );
        }
    }
}
