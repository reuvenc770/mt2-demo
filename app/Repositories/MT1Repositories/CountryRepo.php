<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\MT1Repositories;

use DB;
use Log;

class CountryRepo {
    public function __construct () {}

    public function getAll () {
        try {
            return DB::connection( 'mt1mail' )
                ->table( 'Country' )
                ->select( 'countryID AS id' , 'countryCode AS name' ) 
                ->where( 'visible' , 1 )
                ->orderBy( 'countryCode' )
                ->get();
        } catch ( \Exception $e ) {
            Log::error( 'CountryRepo Error:: ' . $e->getMessage() );
        }
    }
}
