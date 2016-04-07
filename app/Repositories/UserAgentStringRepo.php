<?php

namespace App\Repositories;

use App\Models\UserAgentString;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class UserAgentStringRepo {
  
    private $userAgentString;

    public function __construct(UserAgentString $userAgentString) {
        $this->userAgentString = $userAgentString;
    }


    
}