<?php

namespace App\Repositories;

use App\Models\EmailFeedLatestData;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;
use App\Repositories\RepoInterfaces\IAwsRepo;
use App\Repositories\RepoTraits\Batchable;

class EmailFeedLatestDataRepo implements IAwsRepo {
    use Batchable;

    private $model;
    const INSERT_THRESHOLD = 10000;
    private $batchActionUpdateData = [];
    private $batchActionUpdateCount = 0;

    private $batchDeviceUpdateData = [];
    private $batchDeviceUpdateCount = 0;

    public function __construct(EmailFeedLatestData $model) {
        $this->model = $model;
    }

    public function getRecordDataFromEid($eid){
        $attrDb = config('databases.connections.attribution.database');
        return $this->model
                    ->join("$attrDb.email_feed_assignments as efa", 'email_feed_latest_data.feed_id', '=', 'efa.feed_id')
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
                        other_fields,
                        created_at,
                        updated_at")
                    ->first();
    }

    private function buildBatchedQuery($batchData) {
        return "INSERT INTO email_feed_latest_data (email_id, feed_id, subscribe_date, capture_date, 
                    attribution_status, first_name, last_name, 
                    address, address2, city, state, zip, country, gender, 
                    ip, phone, source_url, dob, other_fields)

                VALUES 

                {$batchData}

                ON DUPLICATE KEY UPDATE
                email_id = email_id,
                feed_id = feed_id,
                subscribe_date = subscribe_date,
                capture_date = capture_date,
                attribution_status = VALUES(attribution_status),
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
                other_fields = VALUES(other_fields)";
    }

    private function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();

        return '('
            . $pdo->quote($row['email_id']) . ','
            . $pdo->quote($row['feed_id']) . ','
            . 'NOW(),'
            . $pdo->quote( Carbon::parse($row['capture_date'])->format('Y-m-d') ) . ','
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
            . $pdo->quote($row['other_fields']) . ')';
    }

    public function batchUpdateDeviceData($row) {
        /**
            This has feed information now
        */
        $pdo = DB::connection()->getPdo();

        if ($this->batchDeviceUpdateCount >= self::INSERT_THRESHOLD) {
            $this->cleanUpActions();

            $this->batchDeviceUpdateData = ['('
                . $pdo->quote($row['email_id']) . ','
                . $pdo->quote($row['feed_id']) . ','
                . $pdo->quote($row['device_type']) . ','
                . $pdo->quote($row['device_name']) . ')'
            ];
            $this->batchDeviceUpdateCount = 1;
        }
        else {
            $this->batchDeviceUpdateData[] = ['('
                . $pdo->quote($row['email_id']) . ','
                . $pdo->quote($row['feed_id']) . ','
                . $pdo->quote($row['device_type']) . ','
                . $pdo->quote($row['device_name']) . ')'
            ];

            $this->batchDeviceUpdateCount++;
        }
    }

    public function cleanupDeviceData() {
        if ($this->batchDeviceUpdateCount > 0) {
            $data = implode(',', $this->batchDeviceUpdateData);
        
            DB::statement("INSERT INTO email_feed_latest_data (email_id, feed_id, device_type, device_name)
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
                carrier = carrier,
                capture_date = capture_date,
                subscribe_date = subscribe_date,
                other_fields = other_fields");

            $this->batchDeviceUpdateData = [];
        }

    }


    public function extractForS3Upload($startPoint) {
        // this start point is a date
        /**
            This needs to be modified to fit record_data on the server
        */

        return $this->model
                    ->join("$attrDb.email_feed_assignments as efa", 'email_feed_latest_data.feed_id', '=', 'efa.feed_id')
                    ->where('email_feed_latest_data.email_id', $eid)
                    ->where("email_feed_latest_data.updated_at > $startpoint")
                    ->select();
    }

    public function extractAllForS3() {
        return $this->model->whereRaw("updated_at > CURDATE() - INTERVAL 7 DAY");
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

    public function updateWithNewAttribution(stdClass $obj) {
        // Unfortunately, the class has already been anonymized

        // '' -> 'UNK'
        $gender = $obj->gender === '' ? 'UNK' : $obj->gender;
        // long2ip if necessary
        $ip = preg_match('/\./', $obj->ip) ? $obj->ip : long2ip($obj->ip);

        $this->model->updateOrCreate(['email_id' => $obj->email_id], [
            'email_id' => $obj->email_id,
            'feed_id' => $obj->feed_id,
            'first_name' => $obj->first_name,
            'last_name' => $obj->last_name,
            'address' => $obj->address,
            'address2' => $obj->address2,
            'city' => $obj->city,
            'state' => $obj->state,
            'zip' => $obj->zip,
            'country' => $obj->country,
            'gender' => $gender,
            'ip' => $ip,
            'phone' => $obj->phone,
            'source_url' => $obj->source_url,
            'dob' => $obj->dob,
            'capture_date' => $obj->capture_date,
            'subscribe_date' => $obj->subscribe_date
        ]);
    }

}
