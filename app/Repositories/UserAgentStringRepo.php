<?php

namespace App\Repositories;

use App\Models\UserAgentString;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;



/**
 *
 */
class UserAgentStringRepo {

    public function __construct(UserAgentString $userAgentStringModel) {
        $this->userAgentStringModel = $userAgentStringModel;
    }


    public function loadData($data) {
        $this->userAgentStringModel->updateOrCreate([
                'user_agent_string' => $data['user_agent_string']
            ], $data);
    }
}