<?php

namespace App\Repositories;

use App\Models\EmailIdHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class EmailIdHistoryRepo {


    private $model;

    public function __construct(EmailIdHistory $model) {
        $this->model = $model;
    }

    public function insertIntoHistory($oldEmailId, $newEmailId) {
        if ($this->model->find($oldEmailId)) {
            // this row already exists, so update

            DB::statement("UPDATE email_id_histories 
                SET old_email_id_list = JSON_MERGE(old_email_id_list, JSON_ARRAY(email_id)), 
                email_id = :new_key
                WHERE email_id = :old_key", 
            [
                ':new_key' => $newEmailId,
                ':old_key' => $oldEmailId
            ]);

        }
        elseif ($this->model->find($newEmailId)) {
            // simplest explanation - something broke, so being defensive
            return;
        }
        else {
            $this->model->insert([
                'email_id' => $newEmailId,
                'old_email_id_list' => DB::raw("JSON_ARRAY($oldEmailId)")
            ]);
        }
    }


}