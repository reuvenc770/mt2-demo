<?php

namespace App\Repositories;

use App\Models\EmailFeedStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class EmailFeedStatusRepo {
    private $model;

    private $batchEmails = [];
    private $batchEmailCount = 0;
    const INSERT_THRESHOLD = 10000;

    public function __construct(EmailFeedStatus $model) {
        $this->model = $model;
    }

    public function insert($emailId, $feedId) {
        DB::statement("INSERT INTO email_feed_statuses (email_id, feed_id, status) 
            VALUES (:email, :feed, 'Active')
            ON DUPLICATE KEY UPDATE
            email_id = email_id,
            feed_id = feed_id,
            status = status", [
                ':email' => $emailId,
                ':feed' => $feedId
            ]);
    }

    public function batchInsert($row) {
        if ($this->batchEmailCount >= self::INSERT_THRESHOLD) {
            $this->executeBatchInsert($this->batchEmails);
            $this->batchEmails = [$this->makeRow($row)];
            $this->batchEmailCount = 1;
        }
        else {
            $this->batchEmails[] = $this->makeRow($row);
            $this->batchEmailCount++;
        }
    }

    public function insertStored() {
        $this->executeBatchInsert($this->batchEmails);
        $this->batchEmails = [];
        $this->batchEmailCount = 0;
    }

    private function executeBatchInsert($rows) {
        $values = implode(',', $rows);

        DB::statement("INSERT INTO email_feed_statuses (email_id, feed_id, status)
            VALUES $values
            ON DUPLICATE KEY UPDATE
            email_id = email_id,
            feed_id = feed_id,
            status = status");
    }

    private function makeRow($row) {
        return '("' . $row['email_id'] . '", "' . $row['feed_id'] . '", "' . $row['status'] . '")';
    }

    public function updateStatus($emailId, $feedId, $status) {
        DB::update("UPDATE email_feed_statuses SET status = ? WHERE email_id = ? AND feed_id = ?", [$status, $emailId, $feedId]);
    } 
}