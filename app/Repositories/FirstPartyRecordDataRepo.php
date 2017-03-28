<?php

namespace App\Repositories;

use App\Models\FirstPartyRecordData;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;
use App\Repositories\RepoInterfaces\IAwsRepo; 

class FirstPartyRecordDataRepo implements IAwsRepo {

    private $model;
    private $batchData = [];
    private $batchDataCount = 0;
    const INSERT_THRESHOLD = 10000;

    private $batchActionUpdateData = [];
    private $batchActionUpdateCount = 0;

    public function __construct(FirstPartyRecordData $model) {
        $this->model = $model;
    }

    public function insert($row) {
        if ($this->batchDataCount >= self::INSERT_THRESHOLD) {

            $this->insertStored();
            $this->batchData = [$this->transformRowToString($row)];
            $this->batchDataCount = 1;
        }
        else {
            $this->batchData[] = $this->transformRowToString($row);
            $this->batchDataCount++;
        }
    }

    public function insertStored() {

        if ($this->batchDataCount > 0) {
            $this->batchData = implode(', ', $this->batchData);

            DB::statement("INSERT INTO first_party_record_data (email_id, feed_id, 
                    first_name, last_name, address, address2, city, state, zip, country, 
                    gender, ip, phone, source_url, dob, capture_date, subscribe_date,
                    last_action_type, last_action_offer_id, last_action_esp_account_id,
                    last_action_date, other_fields)

                VALUES 

                {$this->batchData}

                ON DUPLICATE KEY UPDATE
                email_id = email_id,
                feed_id = feed_id,
                first_name = VALUES(first_name),
                last_name = VALUES(last_name),
                address = VALUES(address),
                address2 = VALUES(address2),
                city = VALUES(city),
                state = VALUES(state),
                zip = VALUES(zip),
                country = VALUES(country),
                gender = VALUES(gender),
                ip = VALUES(ip),
                phone = VALUES(phone),
                source_url = VALUES(source_url),
                dob = VALUES(dob),
                capture_date = VALUES(capture_date),
                subscribe_date = VALUES(subscribe_date),
                last_action_type = VALUES(last_action_type),
                last_action_offer_id = VALUES(last_action_offer_id),
                last_action_esp_account_id = VALUES(last_action_esp_account_id),
                last_action_date = VALUES(last_action_date),
                other_fields = VALUES(other_fields)");

            $this->batchData = [];
            $this->batchDataCount = 0;
        }
    }

    private function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();

        return '('
            . $pdo->quote($row['email_id']) . ','
            . $pdo->quote($row['feed_id']) . ','
            . $pdo->quote($row['first_name']) . ','
            . $pdo->quote($row['last_name']) . ','
            . $pdo->quote($row['address']) . ','
            . $pdo->quote($row['address2']) . ','
            . $pdo->quote($row['city']) . ','
            . $pdo->quote($row['state']) . ','
            . $pdo->quote($row['zip']) . ','
            . $pdo->quote($row['country']) . ','
            . $pdo->quote($row['gender']) . ','
            . $pdo->quote($row['ip']) . ','
            . $pdo->quote($row['phone']) . ','
            . $pdo->quote($row['source_url']) . ','
            . $pdo->quote($row['dob']) . ','
            . $pdo->quote( Carbon::parse($row['capture_date'])->format('Y-m-d') ) . ','
            . 'NOW(), NULL, NULL, NULL, NULL'
            . $pdo->quote($row['other_fields']) // other fields empty for now
            . ')';
    }

    public function updateDeviceData($data) {
        if (sizeof($data) > 0) {
            $data = implode(',', $data);
        
            DB::statement("INSERT INTO first_party_record_data (email_id, device_type, device_name, carrier)
            
                VALUES
            
                $data
            
                ON DUPLICATE KEY UPDATE
                email_id = email_id,
                first_name = first_name,
                last_name = last_name,
                address = address,
                address2 = address2,
                city = city,
                state = state,
                zip = zip,
                country = country,
                gender = gender,
                ip = ip,
                phone = phone,
                source_url = source_url,
                dob = dob,
                device_type = values(device_type),
                device_name = values(device_name),
                carrier = values(carrier),
                capture_date = capture_date,
                subscribe_date = subscribe_date,
                other_fields = other_fields
            ");
        }

    }

    public function getDeliverableStatus($emailId, $feedId) {
        $data = $this->model->where('email_id', $emailId)->where('feed_id', $feedId)->get();

        if ($data) {
            return $data->last_action_type === 'None' ? 1 : 0;
        }
        else {
            return 1; // default set to deliverable ... 
        }
    }

    public function isUnique($emailId, $feedId) {
        return $this->model->where('email_id', $emailId)->where('feed_id', $feedId)->count() === 0;
    }


    public function updateActionData($row) {
        $pdo = DB::connection()->getPdo();

        if ($this->batchActionUpdateCount >= self::INSERT_THRESHOLD) {
            $this->cleanUpActions();

            $this->batchActionUpdateData = ['(' 
                . $pdo->quote($row['email_id']) . ','
                . $pdo->quote($row['feed_id']) . ','
                . $pdo->quote($row['action_type']) . ','
                . $pdo->quote($row['offer_id']) . ','
                . $pdo->quote($row['esp_account_id']) . ','
                . $pdo->quote($row['date']) . ')'];

            $this->batchActionUpdateCount = 1;
        }
        else {
            $this->batchActionUpdateData = ['(' 
                . $pdo->quote($row['email_id']) . ','
                . $pdo->quote($row['feed_id']) . ','
                . $pdo->quote($row['action_type']) . ','
                . $pdo->quote($row['offer_id']) . ','
                . $pdo->quote($row['esp_account_id']) . ','
                . $pdo->quote($row['date']) . ')'];

            $this->batchActionUpdateCount++;
        }
    }

    public function cleanUpActions() {
        if ($this->batchActionUpdateCount > 0) {

            $inserts = implode(',', $this->batchActionUpdateData);

            DB::statement("INSERT INTO first_party_record_data 
                (email_id, feed_id, last_action_type, last_action_offer_id, last_action_esp_account_id, last_action_date)
                VALUES

                $inserts

                ON DUPLICATE KEY UPDATE
                email_id = email_id,
                feed_id = feed_id,
                first_name = first_name,
                last_name = last_name,
                address = address,
                address2 = address2,
                city = city,
                state = state,
                zip = zip,
                country = country,
                gender = gender,
                ip = ip,
                phone = phone,
                source_url = source_url,
                dob = dob,
                device_type = device_type,
                device_name = device_name,
                carrier = carrier,
                capture_date = capture_date,
                subscribe_date = subscribe_date,
                last_action_date = VALUES(last_action_date),
                last_action_offer_id = VALUES(last_action_offer_id),
                last_action_esp_account_id = VALUES(last_action_esp_account_id),
                last_action_type_id = VALUES(last_action_type_id),
                other_fields = other_fields");

            $this->batchActionUpdateData = [];
            $this->batchActionUpdateCount = 0;

        }
    }

    public function extractForS3Upload($startPoint) {
        // this start point is a date
        return $this->select('*', DB::raw('IF(last_action_type IS NULL, 1, 0) as is_deliverable'))
                    ->model
                    ->whereRaw("updated_at > $startPoint");
    }

    public function extractAllForS3() {
        return $this->select('*', DB::raw('IF(last_action_type IS NULL, 1, 0) as is_deliverable'))->model;
    }

    public function specialExtract($data) {
        return $this->select('*', DB::raw('IF(last_action_type IS NULL, 1, 0) as is_deliverable'))->model;
    }

    public function mapForS3Upload($row) {
        $pdo = DB::connection('redshift')->getPdo();
        
        return $pdo->quote($row->email_id) . ','
            . $pdo->quote($row->feed_id) . ','
            . $pdo->quote($row->is_deliverable) . ','
            . $pdo->quote($row->first_name) . ','
            . $pdo->quote($row->last_name) . ','
            . $pdo->quote($row->address) . ','
            . $pdo->quote($row->address2) . ','
            . $pdo->quote($row->city) . ','
            . $pdo->quote($row->state) . ','
            . $pdo->quote($row->zip) . ','
            . $pdo->quote($row->country) . ','
            . $pdo->quote($row->gender) . ','
            . $pdo->quote($row->ip) . ','
            . $pdo->quote($row->phone) . ','
            . $pdo->quote($row->source_url) . ','
            . $pdo->quote($row->dob) . ','
            . $pdo->quote(str_replace('"', '', $row->device_type)) . ','
            . $pdo->quote(str_replace('"', '', $row->device_name)) . ','
            . $pdo->quote(str_replace('"', '', $row->carrier)) . ','
            . $pdo->quote($row->capture_date) . ','
            . $pdo->quote($row->subscribe_date) . ','
            . $pdo->quote($row->last_action_offer_id) . ','
            . $pdo->quote($row->last_action_date) . ','
            . $pdo->quote($row->other_fields) . ','
            . $pdo->quote($row->created_at) . ','
            . $pdo->quote($row->updated_at);
    }

    public function getConnection() {
        return $this->model->getConnectionName();
    }

    public function getTableName() {
        return config('database.connections.mysql.database') . '.' . $this->model->getTable();
    }

    public function addNewRows(array $data) {
        // insert these into the table ... 

        foreach ($data as $row) {
            // we are sending in plenty of 3rd party as well so we need a check
            if (1 === (int)$row['party']) {
                // sensible defaults
                if (!isset($row['other_fields']) || '' === $row['other_fields']) {
                    $row['other_fields'] = '{}';
                }
                
                $this->insert($row);
            }
        }

        $this->insertStored();
    }
}
