<?php

namespace App\Repositories;

use App\Models\EmailFeedAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Repositories\RepoTraits\Batchable;

class EmailFeedActionRepo {
    use Batchable;
  
    private $model;

    public function __construct(EmailFeedAction $model) {
        $this->model = $model;
    } 

    public function buildBatchableQuery($data) {
        return "INSERT INTO email_feed_actions (email_id, feed_id, action_date, status)
        VALUES

        $data

        ON DUPLICATE KEY UPDATE
        email_id = email_id,
        feed_id = feed_id,
        action_date = VALUES(action_date),
        status = VALUES(status),
        created_at = created_at,
        updated_at = NOW()";
    }

    public function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();
        return '(' 
            . $row['email_id'] . ', ' 
            . $row['feed_id'] . ', ' 
            . $pdo->quote($row['action_date']) . ', ' 
            . $pdo->quote($row['status']) . ')';
    }
}