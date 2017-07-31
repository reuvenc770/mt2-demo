<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Repositories\CpmListProfileReportRepo;

class CpmListProfileReportService {
    protected $repo;

    public function __construct ( CpmListProfileReportRepo $repo ) {
        $this->repo = $repo;
    }

    public function getCurrentMonthsPricings () {
        return $this->repo->getCurrentMonthsPricings();
    }

    public function getCountsForDeploy ( $deployId ) {
        return $this->repo->getCountsForDeploy( $deployId );
    }

    public function clearForCakeOfferId ( $cakeOfferId ) {
        return $this->repo->clearForCakeOfferId( $cakeOfferId );
    }

    public function saveReport ( $records ) {
        return $this->repo->saveReport( $records );
    }
}
