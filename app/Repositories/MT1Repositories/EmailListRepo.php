<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\EmailList;
use App\Repositories\RepoInterfaces\Mt1Import;

class EmailListRepo implements Mt1Import {
    protected $model;

    public function __construct ( EmailList $model ) {
        $this->model = $model;
    }

    public function insertToMt1($data) {
        #$this->model->updateOrCreate(['email_user_id' => $data['email_user_id']], $data);
    }
}
