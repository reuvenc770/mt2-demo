<?php

namespace App\Repositories;

use App\Models\ThirdPartyEmailStatus;
use App\Repositories\RepoTraits\Batchable;
use DB;

class ThirdPartyEmailStatusRepo {
    use Batchable;

    private $model;

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
                                            ELSE
                                                CASE 
                                                    WHEN (last_action_datetime < values(last_action_datetime) OR last_action_datetime IS NULL) THEN values(last_action_esp_account_id)
                                                    WHEN (last_action_datetime > values(last_action_datetime)) THEN last_action_esp_account_id
                                                    WHEN (last_action_type = 'Conversion') THEN last_action_esp_account_id
                                                    WHEN (values(last_action_type) = 'Conversion') THEN values(last_action_esp_account_id)
                                                    WHEN (last_action_type = 'Click') THEN last_action_esp_account_id
                                                    WHEN values(last_action_type) = 'Click' THEN values(last_action_esp_account_id)
                                                    ELSE last_action_esp_account_id
                                                END
                                        END,

            last_action_offer_id = CASE
                                        WHEN (last_action_offer_id IS NULL or last_action_offer_id = 0) THEN values(last_action_offer_id)
                                        ELSE
                                            CASE 
                                                WHEN (last_action_datetime < values(last_action_datetime) OR last_action_datetime IS NULL) THEN values(last_action_offer_id)
                                                WHEN (last_action_datetime > values(last_action_datetime)) THEN last_action_offer_id
                                                WHEN (last_action_type = 'Conversion') THEN last_action_offer_id
                                                WHEN (values(last_action_type) = 'Conversion') THEN values(last_action_offer_id)
                                                WHEN (last_action_type = 'Click') THEN last_action_offer_id
                                                WHEN values(last_action_type) = 'Click' THEN values(last_action_offer_id)
                                                ELSE last_action_offer_id
                                            END
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

            $this->batchInsert($row);
        }

        $this->insertStored();
    }

    public function getTableName() {
        return config('database.connections.mysql.database') . '.' . $this->model->getTable();
    }
    
}