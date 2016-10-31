<?php

namespace App\Repositories;

use App\Models\ListProfileBaseTable;
use DB;

class ListProfileBaseTableRepo {

    private $model;

    public function __construct(ListProfileBaseTable $model) {
        $this->model = $model;
    }


    public function insert($row) {
        $this->model->insert($row);
    }


    public function suppressWithListIds($listIds) {

        if (count($listIds) > 0) {
            $suppDb = config('database.connections.suppression.database');
            $table = $this->model->getTable();

            $listIds = '(' . implode(',', $listIds) . ')';

            $query = $this->model
                    ->select("$table.*")
                    ->leftJoin("$suppDb.suppression_list_suppressions as sls", function($join) use($table, $listIds) {
                        $join->on("$table.email_address", '=', 'sls.email_address');
                        $join->on('sls.suppression_list_id', 'in', DB::raw($listIds));
                    })  
                    ->whereNull('sls.email_address');
            return $query;
        }
        else {
            return $this->model;
        }
        
    }
}