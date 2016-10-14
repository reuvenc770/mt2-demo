<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\Country;
use Log;

class CountryRepo {
    protected $model;

    public function __construct ( Country $model ) {
        $this->model = $model;
    }

    public function getAll () {
        try {
            return $this->model->select( 'countryID AS id' , 'countryName AS name' , 'countryCode AS code' )
                ->where( 'visible' , 1 )
                ->orderBy( 'countryCode' )
                ->get();
        } catch ( \Exception $e ) {
            Log::error( 'CountryRepo Error: ' . $e->getMessage() );
        }
    }
}
