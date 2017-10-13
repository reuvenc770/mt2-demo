<?php

namespace App\Repositories;

use App\Models\ThirdPartyEmailStatus;
use App\Repositories\RepoTraits\Batchable;
use DB;

class ThirdPartyEmailStatusRepo {
    use Batchable;

    private $model;
    private $batchNewDataCount = 0;
    private $batchNewData = [];
    private $batchInsertNewQuery = '';

    public function __construct(ThirdPartyEmailStatus $model) {
        $this->model = $model;
    }

    public function getActionStatus($emailId) {
        $row = $this->model->where('email_id', $emailId)->first();

        if ($row) {
            return $row->last_action_type;
        }
        else {
            return null;
        }
    }

    public function getLastActionTime($emailId) {
        $row = $this->model->where('email_id')->first();

        if ($row) {
            return $row->last_action_datetime;
        }
        else {
            return null;
        }
    }

    private function buildBatchedQuery($data) {
        return "INSERT INTO third_party_email_statuses (email_id, last_action_type,
            last_action_offer_id, last_action_datetime, last_action_esp_account_id,
            created_at, updated_at)

            VALUES

            $data

            ON DUPLICATE KEY UPDATE
            email_id = email_id,
            last_action_type = CASE 
                                    WHEN (last_action_type IS NULL OR last_action_type = 'None') THEN values(last_action_type)
                                    WHEN (last_action_datetime < values(last_action_datetime) OR last_action_datetime IS NULL) THEN values(last_action_type)
                                    WHEN (last_action_datetime > values(last_action_datetime)) THEN last_action_type
                                    WHEN (last_action_type = 'Conversion' OR values(last_action_type) = 'Conversion') THEN 'Conversion'
                                    WHEN (last_action_type = 'Click' OR values(last_action_type) = 'Click') THEN 'Click' # conversions handled above
                                    ELSE 'Open' # already handled conversions and clicks above
                                END,
                                
            last_action_datetime = CASE
                                        WHEN (last_action_datetime IS NULL) THEN values(last_action_datetime)
                                        ELSE GREATEST(last_action_datetime, values(last_action_datetime))
                                    END,

            last_action_esp_account_id = CASE
                                            WHEN (last_action_esp_account_id IS NULL or last_action_esp_account_id = 0) THEN values(last_action_esp_account_id)
                                            WHEN (last_action_datetime < values(last_action_datetime) OR last_action_datetime IS NULL) THEN values(last_action_esp_account_id)
                                            WHEN (last_action_datetime > values(last_action_datetime)) THEN last_action_esp_account_id
                                            WHEN (last_action_type = 'Conversion') THEN last_action_esp_account_id
                                            WHEN (values(last_action_type) = 'Conversion') THEN values(last_action_esp_account_id)
                                            WHEN (last_action_type = 'Click') THEN last_action_esp_account_id
                                            WHEN values(last_action_type) = 'Click' THEN values(last_action_esp_account_id)
                                            ELSE last_action_esp_account_id
                                        END,

            last_action_offer_id = CASE
                                        WHEN (last_action_offer_id IS NULL or last_action_offer_id = 0) THEN values(last_action_offer_id)
                                        WHEN (last_action_datetime < values(last_action_datetime) OR last_action_datetime IS NULL) THEN values(last_action_offer_id)
                                        WHEN (last_action_datetime > values(last_action_datetime)) THEN last_action_offer_id
                                        WHEN (last_action_type = 'Conversion') THEN last_action_offer_id
                                        WHEN (values(last_action_type) = 'Conversion') THEN values(last_action_offer_id)
                                        WHEN (last_action_type = 'Click') THEN last_action_offer_id
                                        WHEN values(last_action_type) = 'Click' THEN values(last_action_offer_id)
                                        ELSE last_action_offer_id
                                    END,
            created_at = created_at,
            updated_at = values(updated_at)";
    }

    private function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();

        $offerId = $row['offer_id'] ? $pdo->quote($row['offer_id']) : 'null';
        $datetime = $row['datetime'] ? $pdo->quote($row['datetime']) : 'null';
        $espAccountId = $row['esp_account_id'] ? $pdo->quote($row['esp_account_id']) : 'null';

        return '('
            . $pdo->quote($row['email_id']) . ','
            . $pdo->quote($row['action_type']) . ','
            . $offerId . ','
            . $datetime . ','
            . $espAccountId . ', NOW() , NOW())';
    }

    public function addNewRows(array $rows) {
        foreach($rows as $row) {
            $row['action_type'] = 'None';
            $row['offer_id'] = null;
            $row['datetime'] = null;
            $row['esp_account_id'] = null;

            $this->batchInsertNew($row);
        }

        $this->insertStoredNew();
    }

    public function getTableName() {
        return config('database.connections.mysql.database') . '.' . $this->model->getTable();
    }

    public function batchInsertNew(array $data) {
        if ($this->batchNewDataCount >= $this->insertThreshold) {
            $this->insertStoredNew();
            $this->batchNewData = [$this->transformRowToString($row)];
            $this->batchNewDataCount = 1;
        }
        else {
            $this->batchNewData[] = $this->transformRowToString($row);
            $this->batchNewDataCount++;
        }
    }

    private function buildBatchedNewQuery($data) {
        return "INSERT INTO third_party_email_statuses (email_id, last_action_type,
            last_action_offer_id, last_action_datetime, last_action_esp_account_id,
            created_at, updated_at)

            VALUES

            $data

            ON DUPLICATE KEY UPDATE
            email_id = email_id,
            last_action_type = VALUES(last_action_type),
            last_action_datetime = VALUES(last_action_datetime),
            last_action_esp_account_id = values(last_action_esp_account_id),
            last_action_offer_id = values(last_action_offer_id),
            created_at = created_at,
            updated_at = values(updated_at)";
    }

    public function insertStoredNew() {
        if ($this->batchNewDataCount > 0) {
            $done = false;
            $attempts = 0;
            $this->batchNewData = implode(', ', $this->batchNewData);
            $query = $this->buildBatchedNewQuery($this->batchNewData);

            while (!$done) {
                if ($attempts < $this->maxRetryAttempts) {
                    try {
                        DB::connection($this->model->getConnectionName())->statement($query);
                        $done = true;
                    }
                    catch (\Exception $e) {
                        $attempts++;
                        sleep(2);
                    }
                }
                else {
                    throw new \Exception(get_called_class() . " method insertStoredNew() failed too many times with {$e->getMessage()}");
                }
                
            }

            $this->batchNewData = [];
            $this->batchNewDataCount = 0;
        }
    }
    
}