<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\MT1Repositories;

use DB;
use Log;

class OfferCategoryRepo {
    public function __construct () {}

    public function getAll () {
        try {
            return DB::connection( 'mt1mail' )
                ->table( 'category_info' )
                ->select( 'category_id AS id' , 'category_name as name' )
                ->where( 'status' , 'A' )
                ->orderBy( 'category_name' )
                ->get();
        } catch ( \Exception $e ) {
            Log::error( "OfferCategoryRepo Error: " . $e->getMessage() );
        }
    }
}
