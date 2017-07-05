<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Repositories;

use App\Models\CakeAffiliate;
use App\Models\CakeRedirectDomain;

class CakeAffiliateRepo {
    protected $aff;
    protected $redirect;

    public function __construct ( CakeAffiliate $aff , CakeRedirectDomain $redirect ) {
        $this->aff = $aff;
        $this->redirect = $redirect;
    }

    public function getAll () {
        return $this->aff->get()->sortBy( 'name' );
    }

    public function getModel () {
        return $this->aff
            ->join( 'cake_redirect_domains' , 'cake_affiliates.id' , '=' , 'cake_redirect_domains.cake_affiliate_id' )
            ->join( 'offer_payout_types' , 'cake_redirect_domains.offer_payout_type_id' , '=' , 'offer_payout_types.id' )
            ->select(
                'cake_affiliates.id' ,
                'cake_affiliates.name' ,
                'offer_payout_types.name as offer_type' ,
                'cake_redirect_domains.id as redirect_domain_id' ,
                'cake_redirect_domains.offer_payout_type_id' ,
                'cake_redirect_domains.redirect_domain'
            );
    }

    public function createOrUpdateAffiliate ( $data ) {
        $this->aff->id = $data[ 'id' ];
        $this->aff->name = $data[ 'name' ];

        $this->aff->save();

        return $this->aff->id;
    }

    public function createOrUpdateRedirect ( $data ) {

    }
}
