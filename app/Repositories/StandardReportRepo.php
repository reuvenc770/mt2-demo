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

    public function getRow($id){
        return $this->reportModel->find($id);
    }
    public function getDeployId ( $internalEspId ) {
        return $this->reportModel->where( "esp_internal_id" , $internalEspId )
            ->first()
            ->pluck( 'external_deploy_id' )
            ->pop();
    }

    public function getInternalEspId ( $deployId ) {
        return $this->reportModel->where( "external_deploy_id" , $deployId )
            ->first()
            ->pluck( 'esp_internal_id' )
            ->pop();
    }
}
