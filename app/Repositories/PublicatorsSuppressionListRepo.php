<?php

namespace App\Repositories;

use App\Models\PublicatorsSuppressionList;

class PublicatorsSuppressionListRepo {
    protected $suppList;

    public function __construct ( PublicatorsSuppressionList $suppList ) {
        $this->suppList = $suppList;
    }

    public function getSuppListIdForAccount($accountName) {
        $this->suppList
             ->select('id')
             ->where('account_name', $accountName)
             ->get();
    }
}
