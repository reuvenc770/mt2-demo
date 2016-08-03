<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Repositories\EmailActionsRepo;

class EmailActionService {
    protected $repo;

    public function __construct ( EmailActionsRepo $repo ) {
        $this->repo = $repo;
    }

    public function getByDateRange ( $dateRange = null ) {
        return $this->repo->getByDateRange( $dateRange );
    }

    public function getAggregatedByDateRange ( $dateRange = null ) {
        return $this->repo->getAggregatedByDateRange( $dateRange );
    }

    public function get ( $emailIdList , $dateRange = null ) {
        return $this->repo->get( $emailIdList , $dateRange );
    } 
}
