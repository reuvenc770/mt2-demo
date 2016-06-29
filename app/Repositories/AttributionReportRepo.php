<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionReport;

class AttributionReportRepo {
    protected $report;

    public function __construct ( AttributionReport $report ) {
        $this->report = $report;
    }

    public function getByClientId ( $clientId , $daysBack ) {
        #returns stats for given client ID
    }

    public function getByDeployId ( $deployId , $daysBack ) {
        #returns stats for given deploy ID
    }

    public function getByDaysBack ( $daysBack ) {
        #returns stats for given days back
    }
}
