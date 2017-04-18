<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Repositories\CpmPricingRepo;

class CpmPricingService {
    protected $repo;

    public function __construct ( CpmPricingRepo $repo ) {
        $this->repo = $repo;
    }

    public function getPricings ( $search = [] ) {
        return $this->repo->getPricings( $search );
    }

    public function create ( $record ) {
        if ( $this->isOverride( $record ) ) {
            $this->createOverride( $record );
        } else {
            $this->createPricing( $record );
        }
    }

    public function update ( $id , $record ) {
        if ( $this->isOverride( $record ) ) {
            $this->updateOverride( $id , $record );
        } else {
            $this->updatePricing( $id , $record );
        }
    }

    protected function isOverride ( $record ) {
        return ( isset( $record[ 'deploy_id' ] ) && is_numeric( $record[ 'deploy_id' ] ) && $record[ 'deploy_id' ] > 0 );
    }

    protected function createPricing ( $record ) {
        return $this->repo->createPricing( $record );
    }

    protected function updatePricing ( $id , $record ) {
        return $this->repo->updatePricing( $id , $record );
    }

    protected function createOverride ( $record ) {
        return $this->repo->createOverride( $record );
    }

    protected function updateOverride ( $id , $record ) {
        return $this->repo->updateOverride( $id , $record );
    }
}
