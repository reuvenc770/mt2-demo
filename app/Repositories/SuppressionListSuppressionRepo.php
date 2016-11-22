<?php

namespace App\Repositories;

use App\Models\SuppressionListSuppression;
use DB;

class SuppressionListSuppressionRepo {

    private $model;

    public function __construct ( SuppressionListSuppression $model ) {
        $this->model = $model;
    }

    public function insert($row) {
        return $this->model->insert($row);
    }

    public function updateOrCreate($data) {
        $this->model->updateOrCreate(['id' => $data['id']], $data);
    }

    public function returnSuppressedWithFeedIds($emails, $listIds) {
        return $this->model
                    ->whereIn('suppression_list_id', $listIds)
                    ->whereIn('email_address', $emails)
                    ->selectRaw('email_address, GROUP_CONCAT(DISTINCT suppression_list_id SEPARATOR ",") as suppression_lists')
                    ->groupBy('email_address')
                    ->get();
    }

}