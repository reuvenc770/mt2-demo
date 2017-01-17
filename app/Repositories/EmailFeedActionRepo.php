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
        return "INSERT INTO email_feed_actions (email_id, feed_id, status)
        VALUES

        $data

        ON DUPLICATE KEY UPDATE
        email_id = email_id,
        feed_id = feed_id,
        status = VALUES(status),
        created_at = created_at,
        updated_at = NOW()";
    }

    public function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();
        return '(' 
            . $row['email_id'] . ', ' 
            . $row['feed_id'] . ', ' 
            . $pdo->quote($row['status']) . ')';
    }

    public function getActionStatus($emailId, $feedId) {
        // either returns Model with properties or null
        return $this->model
                    ->where('email_id', $emailId)
                    ->where('feed_id', $feedId)
                    ->select('status')
                    ->first();
    }

    public function getCurrentAttributedStatus($emailId) {
        $attrDb = config('database.attribution.database');

        return $this->model
                    ->join("$attrDb.email_feed_assignments as efa", "email_feed_action.email_id", '=', 'efa.email_id')
                    ->where('email_id', $emailId)
                    ->select('efa.feed_id', 'email_feed_action.status')
                    ->first();
    }
}