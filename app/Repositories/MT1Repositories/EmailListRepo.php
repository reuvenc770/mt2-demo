<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\EmailList;
use App\Models\MT1Models\LiveEmailList;
use App\Repositories\RepoInterfaces\Mt1Import;

class EmailListRepo implements Mt1Import {
    protected $model;
    private $liveModel;

    public function __construct ( EmailList $model, LiveEmailListModel $liveModel ) {
        $this->model = $model;
        $this->liveModel = $liveModel;
    }

    public function insertToMt1($data) {
        $this->liveModel->updateOrCreate(['email_user_id' => $data['email_user_id']], $data);
    }
}
