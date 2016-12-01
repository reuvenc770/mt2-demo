<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Services\ServiceTraits\PaginateList;
use App\Repositories\AmpReportRepo;

class AmpReportService {
    use PaginateList;

    protected $repo;

    public function __construct ( AmpReportRepo $repo ) {
        $this->repo = $repo;
    }

    public function getModel () {
        return $this->repo->getModel();
    }

    public function getPageData ( $id ) {
        return $this->repo->getPageData( $id );
    }

    public function saveReport ( $name , $reportId ) {
        $this->repo->saveReport( $name , $reportId );
    }

    public function updateReport ( $systemId , $name , $reportId ) {
        $this->repo->updateReport( $systemId , $name , $reportId );
    }
}
