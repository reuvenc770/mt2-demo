<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\VendorSuppList;
use App\Models\EtlPickup;
use 
use DB;

class VendorSuppListRepo {
    protected $model;
    protected $etlPickup;
    const LAST_ID_FIELD = 'LastVendorSuppId';

    public function __construct ( VendorSuppList $model, EtlPickup $etlPickup ) {
        $this->model = $model;
        $this->etlPickup = $etlPickup;
    }

    public function pullForSync($lookback) {
        $id = $this->etlPickup->where('name', self::LAST_ID_FIELD)->stop_point;
        return $this->model->where('vendorSuppressionListID', '>=', $id);
    }
}