<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Services;

use App\Repositories\AttributionAggregatorRepo;

class AttributionAggregatorService {
    const RUN_STANDARD = 'standard';
    const RUN_CPM = 'cpm';

    protected $repo;
    protected $modelId;

    public function __construct ( AttributionAggregatorRepo $repo ) {
        $this->repo = $repo;
    }

    public function standardRun ( $dateRange , $modelId = null ) {
        $this->repo->standardRun( $dateRange , $modelId );
    }

    public function cpmRun ( $offerId , $dateRange , $modelId = null ) {
        $this->repo->cpmRun( $offerId , $dateRange , $modelId );
    }
}
