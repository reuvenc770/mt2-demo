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
        $affiliateId = 0;
        $newAffiliatePresent = ( isset( $data[ 'new_affiliate_id' ] ) && isset( $data[ 'new_affiliate_name' ] ) );
        if ( $newAffiliatePresent ) {
            $this->repo->createAffiliate( [
                'id' => (int)$data[ 'new_affiliate_id' ] ,
                'name' => $data[ 'new_affiliate_name' ]
            ] );

            $affiliateId = $data[ 'new_affiliate_id' ];
        } else {
            $affiliateId = $data[ 'id' ];
        }

        return $this->repo->createOrUpdateRedirect( [
            'id' => ( isset( $data[ 'redirect_domain_id' ] ) ? $data[ 'redirect_domain_id' ] : null ) ,
            'cake_affiliate_id' => $affiliateId ,
            'offer_payout_type_id' => $data[ 'offer_payout_type_id' ] ,
            'redirect_domain' => $data[ 'redirect_domain' ]
        ] );
    }
}
