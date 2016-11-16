<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\SuppressListOrange;

class SuppressListOrangeRepo {
    protected $model;

    public function __construct ( SuppressListOrange $model ) {
        $this->model = $model;
    }

    public function pullForSync ( $lookback ) {
        return $this->model
                    ->join( 'SuppressionReason as sr' , 'suppress_list_orange.suppressionReasonID' , '=' , 'sr.suppressionReasonID' )
                    ->where( 'dateTimeSuppressed' , '>=' , DB::raw("CURDATE() - INTERVAL $lookback DAY") );
    }
}
