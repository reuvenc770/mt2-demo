<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Repositories\CpmPricingRepo;
use App\Services\ServiceTraits\PaginateList;

class CpmPricingService {
    use PaginateList;

    public function __construct ( CpmPricingRepo $repo ) {
        $this->repo = $repo;
    }

    public function getModel () {
        return $this->repo->getModel();
    }

    public function create ( $record ) {
        return $this->createPricing( $record );
    }

    public function update ( $id , $record ) {
        return $this->updatePricing( $id , $record );
    }

    protected function createPricing ( $record ) {
        return $this->repo->createPricing( $record );
    }

    protected function updatePricing ( $id , $record ) {
        return $this->repo->updatePricing( $id , $record );
    }
}
