<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\AdvertiserInfo;
use Log;
use DB;

class AdvertiserInfoRepo {
    protected $model;

    public function __construct ( AdvertiserInfo $model ) {
        $this->model = $model;
    }

    public function getAll () {
        try {
            return $this->model->select( 'advertiser_id as id' , 'advertiser_name as name' )
                ->where( [
                    [ 'status' , 'A' ] ,
                    [ 'test_flag' , 'N' ]
                ] )
                ->orderBy( 'advertiser_id' , 'DESC' )
                ->get();
        } catch ( \Exception $e ) {
            Log::error( "AdvertiserInfoRepo Error:: " . $e->getMessage() );
        }
    }

    public function pullForSync($lookback) {
        return $this->model;
    }
    

}
