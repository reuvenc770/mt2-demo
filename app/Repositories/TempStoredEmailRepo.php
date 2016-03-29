<?php

namespace App\Repositories;

use App\Models\TempStoredEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class TempStoredEmailRepo {

    private $emailModel;

    public function __construct(TempStoredEmail $emailModel) {
        $this->emailModel = $emailModel;
    }

    public function insert($data) {
        $this->emailModel->insert($data);
    }

}