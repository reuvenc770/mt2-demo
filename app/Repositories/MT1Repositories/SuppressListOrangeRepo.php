<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\SuppressListOrange;
use App\Models\EtlPickup;

class SuppressListOrangeRepo {
    const LAST_ID_FIELD = 'SuppressListOrangeId';

    protected $model;

    public function __construct ( SuppressListOrange $model ) {
        $this->model = $model;
    }

    public function pullForSync ( $lookback ) {
        $etlPickup = new EtlPickup();
        $id = $etlPickup->where('name', self::LAST_ID_FIELD)->first()->stop_point;

        return $this->model
                    ->join( 'SuppressionReason as sr' , 'suppress_list_orange.suppressionReasonID' , '=' , 'sr.suppressionReasonID' )
                    ->where( 'sid' , '>=' , $id );
    }
}
