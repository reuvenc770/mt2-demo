<?php

namespace App\Repositories\MT1Repositories;

use App\Models\ModelTraits\ModelCacheControl;
use App\Models\MT1Models\AdvertiserInfo;
use Log;

class AdvertiserRepo {
    use ModelCacheControl;

    protected $model;

    public function __construct ( AdvertiserInfo $model ) {
        $this->model = $model;
    }

    public function getAll () {
        try {
            return $this->model::select( 'advertiser_id as id' , 'advertiser_name as name' )
                ->where( [
                    [ 'status' , 'A' ] ,
                    [ 'test_flag' , 'N' ]
                ] )
                ->orderBy( 'advertiser_id' , 'DESC' )
                ->get();
        } catch ( \Exception $e ) {
            Log::error( "AdvertiserRepo Error:: " . $e->getMessage() );
        }
    }
}
