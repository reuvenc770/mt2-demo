<?php

namespace App\Repositories;

use App\Models\RecordData;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;
use App\Repositories\RepoInterfaces\IAwsRepo;
use App\Repositories\RepoTraits\Batchable;

class RecordDataRepo implements IAwsRepo {
    use Batchable;

    private $model;
    const INSERT_THRESHOLD = 10000;
    private $batchActionUpdateData = [];
    private $batchActionUpdateCount = 0;

    public function __construct(RecordData $model) {
        $this->model = $model;
    }

    public function getRecordDataFromEid($eid){
        return $this->model
                    ->where('email_id', $eid)
                    ->selectRaw("email_id,
                        first_name,
                        last_name,
                        address,
                        address2,
                        city,
                        state,
                        zip,
                        country,
                        gender,
                        ip,
                        phone,
                        source_url,
                        dob,
                        device_type,
                        device_name,
                        carrier,
                        capture_date,
                        subscribe_date,
                        last_action_offer_id,
                        last_action_date,
                        other_fields,
                        created_at,
                        updated_at")
                    ->first();
    }

    private function buildBatchedQuery($batchData) {
        return "INSERT INTO record_data (email_id, is_deliverable, first_name, last_name, 
                    address, address2, city, state, zip, country, gender, 
                    ip, phone, source_url, dob, capture_date, subscribe_date, other_fields)

                VALUES 

                {$batchData}

                ON DUPLICATE KEY UPDATE
                email_id = email_id,
                is_deliverable = VALUES(is_deliverable),
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
                other_fields = VALUES(other_fields)";
    }

    private function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();

        return '('
            . $pdo->quote($row['email_id']) . ','
            . $pdo->quote($row['is_deliverable']) . ','
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
            . 'NOW(),'
            . $pdo->quote($row['other_fields']) 
            . ')';
    }

    public function updateDeviceData($data) {
        if (sizeof($data) > 0) {
            $data = implode(',', $data);
        
            DB::statement("INSERT INTO record_data (email_id, device_type, device_name, carrier)
            
                VALUES
            
                $data
            
                ON DUPLICATE KEY UPDATE
                email_id = email_id,
                is_deliverable = is_deliverable,
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

    public function getDeliverableStatus($emailId) {
        $data = $this->model->find($emailId);

        if ($data) {
            return $data->is_deliverable;
        }
        else {
            return 1; // default set to deliverable ... 
        }
    }

    public function updateActionData($emailId, $actionDate) {
        $pdo = DB::connection()->getPdo();

        if ($this->batchActionUpdateCount >= self::INSERT_THRESHOLD) {

            $this->cleanUpActions();

            $this->batchActionUpdateData = ['(' 
                . $pdo->quote($emailId) . ', 1,'
                . $pdo->quote($actionDate) . ')'];
            $this->batchActionUpdateCount = 1;
        }
        else {
            $this->batchActionUpdateData[] = '(' 
                . $pdo->quote($emailId) . ', 1,'
                . $pdo->quote($actionDate) . ')';

            $this->batchActionUpdateCount++;
        }
    }

    public function cleanUpActions() {
        if ($this->batchActionUpdateCount > 0) {

            $inserts = implode(',', $this->batchActionUpdateData);

            DB::statement("INSERT INTO record_data (email_id, is_deliverable, last_action_date)
                VALUES

                $inserts

                ON DUPLICATE KEY UPDATE
                email_id = email_id,
                is_deliverable = VALUES(is_deliverable),
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
                other_fields = other_fields");

            $this->batchActionUpdateData = [];
            $this->batchActionUpdateCount = 0;

        }
    }

    public function extractForS3Upload($startPoint) {
        // this start point is a date
        return $this->model->whereRaw("updated_at > $startPoint");
    }

    public function extractAllForS3() {
        return $this->model;
    }

    public function mapForS3Upload($row) {
        $pdo = DB::connection('redshift')->getPdo();
        
        return $pdo->quote($row->email_id) . ','
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

}
