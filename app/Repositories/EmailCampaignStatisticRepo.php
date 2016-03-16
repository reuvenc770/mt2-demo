<?php

namespace App\Repositories;

use App\Models\EmailCampaignStatistic;

class EmailCampaignStatistic {
    protected $model;

    public function __construct(EmailCampaignStatistic $model){
        $this->model = $model;
    }

    public function insertOrUpdate($row, $actionType) {
        /*
            1. Get action type.
            2. Find if row exists:
                a. If it does not, insert and set appropriate rows
                b. else: update appropriate rows
        */
    }



}