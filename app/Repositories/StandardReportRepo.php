<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\StandardReport;

class StandardReportRepo {
    protected $reportModel;

    public function __construct ( StandardReport $reportModel ) {
        $this->reportModel = $reportModel;
    }

    public function getDeployId ( $internalEspId ) {
        return $this->reportModel->where( "esp_internal_id" , $internalEspId )
            ->first()
            ->pluck( 'id' )
            ->pop();
    }
}
