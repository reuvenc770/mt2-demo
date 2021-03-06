<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\Interfaces\IScheduledFilter;
use DB;
use App\Repositories\RepoTraits\Batchable;

class AttributionScheduleRepo {
    use Batchable;

    protected $model;

    public function __construct ( IScheduledFilter $model ) {
        $this->model = $model;
    }

    public function getRecordsByDate($date){
        return $this->model->where("trigger_date", $date);
    }

    public function insertSchedule($emailId, $date){
        return DB::connection("attribution")->statement(
            "INSERT INTO {$this->model->getTable()} (email_id, trigger_date)
            VALUES(:id, :trigger_date)
            ON DUPLICATE KEY UPDATE
            email_id = email_id, trigger_date = VALUES(trigger_date)",
            array(
                ':id' => $emailId,
                ':trigger_date' => $date,

            )
        );
    }

    public function bulkDelete($emails){
        return $this->model->whereIn("email_id", $emails)->delete();
    }

    public function insertScheduleBulk($emails){
        DB::connection("attribution")->statement(
            "INSERT INTO {$this->model->getTable()} (email_id, trigger_date, created_at, updated_at)
        VALUES
                    " . join(' , ', $emails) . "
        ON DUPLICATE KEY UPDATE
        email_id = email_id, trigger_date = VALUES(trigger_date), updated_at = VALUES(updated_at)");
    }

    public function addNewRows(array $rows) {
        $emails = [];

        foreach($rows as $row) {
            $emails[] = '(' . $row['email_id'] . ', CURDATE(), NOW(), NOW())';
        }

        $this->insertScheduleBulk($emails);
    }

    public function getTableName() {
        return config('database.connections.attribution.database') . '.' . $this->model->getTable();
    }

    private function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();

        return '('
            . $pdo->quote($row['email_id']) . ','
            . $pdo->quote($row['trigger_date']) . ', NOW(), NOW()'
            . ')';
    }

    private function buildBatchedQuery(&$batchedData) {
        return "INSERT INTO {$this->model->getTable()} (email_id, trigger_date, created_at, updated_at)

        VALUES

        $batchedData

        ON DUPLICATE KEY UPDATE
        email_id = email_id,
        trigger_date = VALUES(trigger_date),
        created_at = created_at,
        updated_at = NOW()";
    }

    public function getExpiringRecordsBetweenIds($date, $startEmailId, $endEmailId) {
        $startEmailId = (int)$startEmailId;
        $endEmailId = (int)$endEmailId;

        if ($startEmailId > 0 && $endEmailId > 0 && $startEmailId <= $endEmailId) {
            return $this->model
                        ->select('email_id')
                        ->where('trigger_date', $date)
                        ->whereRaw("email_id BETWEEN $startEmailId AND $endEmailId")
                        ->pluck("email_id")->all();
        }
        else {
            return null;
        }
    }

    public function nextNRows($startEmailId, $offset) {
        return $this->model
            ->where('email_id', '>=', $startEmailId)
            ->orderBy('email_id')
            ->skip($offset)
            ->first()['email_id'];
    }

    public function getMinEmailIdForDate($date) {
        return $this->model->where('trigger_date', $date)->min('email_id');
    }

    public function getMaxEmailIdForDate($date) {
        return $this->model->where('trigger_date', $date)->max('email_id');
    }
}