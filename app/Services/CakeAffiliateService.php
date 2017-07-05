<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Services;

use App\Repositories\CakeAffiliateRepo;
use App\Services\ServiceTraits\PaginateList;

class CakeAffiliateService {
    use PaginateList;

    protected $repo;

    public function __construct ( CakeAffiliateRepo $repo ) {
        $this->repo = $repo;
    }

    public function getAll () {
        return $this->repo->getAll();
    }

    public function getModel () {
        return $this->repo->getModel();
    }

    public function updateOrCreate ( $data ) {
        $affiliateId = $data[ 'id' ];

        if ( isset( $data[ 'new_affiliate_id' ] ) ) {

        }
    }
}
