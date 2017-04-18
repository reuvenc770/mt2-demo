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
            last_action_type = IF(last_action_datetime < values(last_action_datetime),    
                                values(last_action_type),
                                IF(last_action_datetime > values(last_action_datetime),
                                    last_action_type,
                                    IF(last_action_type IS NULL OR last_action_type = 'None',
                                        values(last_action_type),
                                        IF(last_action_type = 'Conversion',
                                            last_action_type,
                                            IF(last_action_type = 'Click',
                                                IF(values(last_action_type) = 'Conversion',
                                                    'Conversion',
                                                    IF(values(last_action_type) = 'Click',
                                                        'Click',
                                                        IF(values(last_action_type) = 'Open', 'Click', 'Click'))),
                                                values(last_action_type)))))),
            last_action_datetime = values(last_action_datetime),
            last_action_esp_account_id = values(last_action_esp_account_id),
            last_action_offer_id = values(last_action_offer_id),
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