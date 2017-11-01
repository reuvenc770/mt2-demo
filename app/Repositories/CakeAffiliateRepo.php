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
        return $this->redirect
            ->leftJoin( 'cake_affiliates' , 'cake_affiliates.id' , '=' , 'cake_redirect_domains.cake_affiliate_id' )
            ->leftJoin( 'offer_payout_types' , 'cake_redirect_domains.offer_payout_type_id' , '=' , 'offer_payout_types.id' )
            ->select(
                'cake_redirect_domains.id as cake_redirect_id' ,
                'cake_affiliates.id' ,
                'cake_affiliates.name' ,
                'offer_payout_types.name as offer_type' ,
                'cake_redirect_domains.id as redirect_domain_id' ,
                'cake_redirect_domains.offer_payout_type_id' ,
                'cake_redirect_domains.redirect_domain'
            );
    }

    public function createAffiliate ( $data ) {
        $this->aff->id = $data[ 'id' ];
        $this->aff->name = $data[ 'name' ];
        $this->aff->save();

        return $this->aff;
    }  

    public function createOrUpdateRedirect ( $data ) {
        return $this->redirect->updateOrCreate([
            'cake_affiliate_id' => $data['cake_affiliate_id'], 
            'offer_payout_type_id' => $data['offer_payout_type_id']
        ], $data );
    }
}
