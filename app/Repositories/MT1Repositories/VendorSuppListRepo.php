<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\VendorSuppList;
use App\Models\EtlPickup;
use DB;

class VendorSuppListRepo {
    protected $model;
    const LAST_ID_FIELD = 'LastVendorSuppId';

    public function __construct (VendorSuppList $model) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        $etlPickup = new EtlPickup(); // Expedient shortcut for a temporary importing job
        $id = $etlPickup->where('name', self::LAST_ID_FIELD)->first()->stop_point;
        return $this->model->where('vendorSuppressionListID', '>=', $id);
    }

    public function isSuppressed ( $record , $listId) {
        return $this->model->where( [
            [ 'list_id' , $listId ] ,
            [ 'email_addr' , $record ]
        ] )->count() > 0;
    }
}
