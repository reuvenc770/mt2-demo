<?php

namespace App\Repositories;

use App\Models\ContentServerAction;
use DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class ContentServerActionRepo {

    private $model;

    public function __construct(ContentServerAction $model) {
        $this->model = $model;
    }

    public function insert($data) {
        $this->model->insert($data);
    }

    public function loadInfile($name) {
        $path = storage_path() . '/' . $name;
        DB::unprepared("
            LOAD DATA LOCAL INFILE '$path' IGNORE INTO TABLE
            content_server_actions FIELDS TERMINATED BY ','
            OPTIONALLY ENCLOSED BY '`' LINES TERMINATED BY '\\n'
            (email_id, sub_id, action_type, send_date, action_time)
        ");
    }

    public function pull($lookback) {
        return $this->model
            ->select(DB::raw("email_id, 
                sub_id, 
                SUM(IF(action_type = 'opener', 1, 0)) AS opens, 
                MIN(CASE WHEN action_type = 'opener' THEN action_time END) AS min_open_date, 
                MAX(CASE WHEN action_type = 'opener' THEN action_time END) AS max_open_date, 
                SUM(IF(action_type = 'clicker', 1, 0)) AS clicks, 
                MIN(CASE WHEN action_type = 'clicker' THEN action_time END) AS min_click_date, 
                MAX(CASE WHEN action_type = 'clicker' THEN action_time END) AS max_click_date"))
            ->whereBetween('send_date', [DB::raw("CURDATE() - INTERVAL $lookback DAY"), DB::raw('CURDATE()')])
            ->groupBy('email_id', 'sub_id')
            ->get();
    }

}