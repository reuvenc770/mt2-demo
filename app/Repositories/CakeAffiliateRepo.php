<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Repositories;

use App\Models\CakeAffiliate;

class CakeAffiliateRepo {
    protected $aff;

    public function __construct ( CakeAffiliate $aff ) {
        $this->aff = $aff;
    }

    public function getAll () {
        return $this->aff->get()->sortBy( 'name' );
    }
}
