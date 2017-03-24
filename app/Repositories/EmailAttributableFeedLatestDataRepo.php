<?php

namespace App\Repositories;

use App\Models\EmailAttributableFeedLatestData;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;
use App\Repositories\RepoInterfaces\IAwsRepo;
use App\Repositories\RepoTraits\Batchable;

class EmailAttributableFeedLatestDataRepo implements IAwsRepo {
    use Batchable;

    private $model;
    const INSERT_THRESHOLD = 10000;
    private $batchActionUpdateData = [];
    private $batchActionUpdateCount = 0;

    private $batchDeviceUpdateData = [];
    private $batchDeviceUpdateCount = 0;

    public function __construct(EmailAttributableFeedLatestData $model) {
        $this->model = $model;
    }

    public function getRecordDataFromEid($eid){
        $attrDb = config('database.connections.attribution.database');
        return $this->model
                    ->join("$attrDb.email_feed_assignments as efa", function($join) { 
                        $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
                        $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                    })
                    ->leftJoin('third_party_email_statuses as tpes', 'efa.email_id', '=', 'tpes.email_id')
                    ->where('efa.email_id', $eid)
                    ->selectRaw("efa.email_id,
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
                        email_attributable_feed_latest_data.subscribe_date,
                        last_action_offer_id,
                        last_action_datetime,
                        other_fields")
                    ->first();
    }

    private function buildBatchedQuery($batchData) {
        return "INSERT INTO email_attributable_feed_latest_data (email_id, feed_id, subscribe_date, capture_date, 
                    attribution_status, first_name, last_name, 
                    address, address2, city, state, zip, country, gender, 
                    ip, phone, source_url, dob, other_fields)

                VALUES 

                {$batchData}

                ON DUPLICATE KEY UPDATE
                email_id = email_id,
                feed_id = feed_id,
                subscribe_date = VALUES(subscribe_date),
                capture_date = VALUES(capture_date),
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
        $subscribeDate = isset($row['subscribe_datetime']) 
                            ? $pdo->quote(Carbon::parse($row['subscribe_datetime'])->format('Y-m-d'))
                            : 'NOW()';

        $dob = $row['dob'] ? $pdo->quote($row['dob']) : 'NULL'; 

        return '('
            . $pdo->quote($row['email_id']) . ','
            . $pdo->quote($row['feed_id']) . ','
            . $subscribeDate . ','
            . $pdo->quote( Carbon::parse($row['capture_date'])->format('Y-m-d') ) . ','
            . $pdo->quote($row['attribution_status']) . ','
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
            . $dob . ','
            . $pdo->quote($row['other_fields']) . ')';
    }

    public function batchUpdateDeviceData($row) {
        $pdo = DB::connection()->getPdo();

        if ($this->batchDeviceUpdateCount >= self::INSERT_THRESHOLD) {
            $this->cleanupDeviceData();

            $this->batchDeviceUpdateData = '('
                . $pdo->quote($row['email_id']) . ','
                . $pdo->quote($row['feed_id']) . ','
                . $pdo->quote($row['device_type']) . ','
                . $pdo->quote($row['device_name']) . ')';
            $this->batchDeviceUpdateCount = 1;
        }
        else {
            $this->batchDeviceUpdateData[] = '('
                . $pdo->quote($row['email_id']) . ','
                . $pdo->quote($row['feed_id']) . ','
                . $pdo->quote($row['device_type']) . ','
                . $pdo->quote($row['device_name']) . ','
                . $pdo->quote($row['carrier']) . ')';

            $this->batchDeviceUpdateCount++;
        }
    }

    public function cleanupDeviceData() {
        if ($this->batchDeviceUpdateCount > 0) {
            $data = implode(',', $this->batchDeviceUpdateData);
        
            DB::statement("INSERT INTO email_attributable_feed_latest_data (email_id, feed_id, device_type, device_name, carrier)
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
                other_fields = other_fields");

            $this->batchDeviceUpdateData = [];
        }

    }

    public function extractForS3Upload($startPoint) {
        // this start point is a date
        $attrDb = config('database.connections.attribution.database');
        $union1 = $this->model
                    ->join("$attrDb.email_feed_assignments as efa", function($join) { 
                        $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                        $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
                    })
                    ->leftJoin('third_party_email_statuses as st', 'email_attributable_feed_latest_data.email_id', '=', 'st.email_id')
                    ->whereRaw("efa.updated_at > $startPoint")
                    ->select('efa.email_id', DB::raw("IF(st.last_action_type = 'None', 1, 0) as is_deliverable"),
                        'first_name', 'last_name', 'address', 'address2', 'city', 'state', 'zip', 'country',
                        'gender', 'ip', 'phone', 'source_url', 'dob', 'device_type', 'device_name', 'carrier',
                        'capture_date', 'email_attributable_feed_latest_data.subscribe_date', 'st.last_action_offer_id', DB::raw("DATE(last_action_datetime) as last_action_date"),
                        "other_fields", 'email_attributable_feed_latest_data.created_at', 'efa.updated_at');

        $union2 = $this->model
                    ->join("$attrDb.email_feed_assignments as efa", function($join) { 
                        $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                        $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
                    })
                    ->leftJoin('third_party_email_statuses as st', 'email_attributable_feed_latest_data.email_id', '=', 'st.email_id')
                    ->whereRaw("email_attributable_feed_latest_data.updated_at > $startPoint")
                    ->select('efa.email_id', DB::raw("IF(st.last_action_type = 'None', 1, 0) as is_deliverable"),
                        'first_name', 'last_name', 'address', 'address2', 'city', 'state', 'zip', 'country',
                        'gender', 'ip', 'phone', 'source_url', 'dob', 'device_type', 'device_name', 'carrier',
                        'capture_date', 'email_attributable_feed_latest_data.subscribe_date', 'st.last_action_offer_id', DB::raw("DATE(last_action_datetime) as last_action_date"),
                        "other_fields", 'email_attributable_feed_latest_data.created_at', 'efa.updated_at');

        return $this->model
                    ->join("$attrDb.email_feed_assignments as efa", function($join) { 
                        $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                        $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
                    })
                    ->leftJoin('third_party_email_statuses as st', 'email_attributable_feed_latest_data.email_id', '=', 'st.email_id')
                    ->whereRaw("st.updated_at > $startPoint")
                    ->union($union1)
                    ->union($union2)
                    ->select('efa.email_id', DB::raw("IF(st.last_action_type = 'None', 1, 0) as is_deliverable"),
                        'first_name', 'last_name', 'address', 'address2', 'city', 'state', 'zip', 'country',
                        'gender', 'ip', 'phone', 'source_url', 'dob', 'device_type', 'device_name', 'carrier',
                        'capture_date', 'email_attributable_feed_latest_data.subscribe_date', 'st.last_action_offer_id', DB::raw("DATE(last_action_datetime) as last_action_date"),
                        "other_fields", 'email_attributable_feed_latest_data.created_at', 'efa.updated_at');
    }

    public function extractAllForS3() {
        $attrDb = config('database.connections.attribution.database');
        $union1 = $this->model
                    ->join("$attrDb.email_feed_assignments as efa", function($join) { 
                        $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                        $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
                    })
                    ->leftJoin('third_party_email_statuses as st', 'email_attributable_feed_latest_data.email_id', '=', 'st.email_id')
                    ->whereRaw("efa.updated_at > CURDATE() - INTERVAL 7 DAY")
                    ->select('efa.email_id', DB::raw("IF(st.last_action_type = 'None', 1, 0) as is_deliverable"),
                        'first_name', 'last_name', 'address', 'address2', 'city', 'state', 'zip', 'country',
                        'gender', 'ip', 'phone', 'source_url', 'dob', 'device_type', 'device_name', 'carrier',
                        'capture_date', 'email_attributable_feed_latest_data.subscribe_date', 'st.last_action_offer_id', DB::raw("DATE(last_action_datetime) as last_action_date"),
                        "other_fields", 'email_attributable_feed_latest_data.created_at', 'efa.updated_at');

        $union2 = $this->model
                    ->join("$attrDb.email_feed_assignments as efa", function($join) { 
                        $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                        $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
                    })
                    ->leftJoin('third_party_email_statuses as st', 'email_attributable_feed_latest_data.email_id', '=', 'st.email_id')
                    ->whereRaw("email_attributable_feed_latest_data.updated_at > CURDATE() - INTERVAL 7 DAY")
                    ->select('efa.email_id', DB::raw("IF(st.last_action_type = 'None', 1, 0) as is_deliverable"),
                        'first_name', 'last_name', 'address', 'address2', 'city', 'state', 'zip', 'country',
                        'gender', 'ip', 'phone', 'source_url', 'dob', 'device_type', 'device_name', 'carrier',
                        'capture_date', 'email_attributable_feed_latest_data.subscribe_date', 'st.last_action_offer_id', DB::raw("DATE(last_action_datetime) as last_action_date"),
                        "other_fields", 'email_attributable_feed_latest_data.created_at', 'efa.updated_at');

        return $this->model
                    ->join("$attrDb.email_feed_assignments as efa", function($join) { 
                        $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                        $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
                    })
                    ->leftJoin('third_party_email_statuses as st', 'email_attributable_feed_latest_data.email_id', '=', 'st.email_id')
                    ->whereRaw("st.updated_at > CURDATE() - INTERVAL 7 DAY")
                    ->union($union1)
                    ->union($union2)
                    ->select('efa.email_id', DB::raw("IF(st.last_action_type = 'None', 1, 0) as is_deliverable"),
                        'first_name', 'last_name', 'address', 'address2', 'city', 'state', 'zip', 'country',
                        'gender', 'ip', 'phone', 'source_url', 'dob', 'device_type', 'device_name', 'carrier',
                        'capture_date', 'email_attributable_feed_latest_data.subscribe_date', 'st.last_action_offer_id', DB::raw("DATE(last_action_datetime) as last_action_date"),
                        "other_fields", 'email_attributable_feed_latest_data.created_at', 'efa.updated_at');
    }

    public function specialExtract($data) {
        // $data is a date
        $data = Carbon::parse($data);
        $attrDb = config('database.connections.attribution.database');
        $union1 = $this->model
                    ->join("$attrDb.email_feed_assignments as efa", function($join) { 
                        $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                        $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
                    })
                    ->leftJoin('third_party_email_statuses as st', 'email_attributable_feed_latest_data.email_id', '=', 'st.email_id')
                    ->whereRaw("efa.updated_at > '$data'")
                    ->select('efa.email_id', DB::raw("IF(st.last_action_type = 'None', 1, 0) as is_deliverable"),
                        'first_name', 'last_name', 'address', 'address2', 'city', 'state', 'zip', 'country',
                        'gender', 'ip', 'phone', 'source_url', 'dob', 'device_type', 'device_name', 'carrier',
                        'capture_date', 'email_attributable_feed_latest_data.subscribe_date', 'st.last_action_offer_id', DB::raw("DATE(last_action_datetime) as last_action_date"),
                        "other_fields", 'email_attributable_feed_latest_data.created_at', 'efa.updated_at');

        $union2 = $this->model
                    ->join("$attrDb.email_feed_assignments as efa", function($join) { 
                        $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                        $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
                    })
                    ->leftJoin('third_party_email_statuses as st', 'email_attributable_feed_latest_data.email_id', '=', 'st.email_id')
                    ->whereRaw("email_attributable_feed_latest_data.updated_at > '$data'")
                    ->select('efa.email_id', DB::raw("IF(st.last_action_type = 'None', 1, 0) as is_deliverable"),
                        'first_name', 'last_name', 'address', 'address2', 'city', 'state', 'zip', 'country',
                        'gender', 'ip', 'phone', 'source_url', 'dob', 'device_type', 'device_name', 'carrier',
                        'capture_date', 'email_attributable_feed_latest_data.subscribe_date', 'st.last_action_offer_id', DB::raw("DATE(last_action_datetime) as last_action_date"),
                        "other_fields", 'email_attributable_feed_latest_data.created_at', 'efa.updated_at');

        return $this->model
                    ->join("$attrDb.email_feed_assignments as efa", function($join) { 
                        $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                        $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
                    })
                    ->leftJoin('third_party_email_statuses as st', 'email_attributable_feed_latest_data.email_id', '=', 'st.email_id')
                    ->whereRaw("st.updated_at > '$data'")
                    ->union($union1)
                    ->union($union2)
                    ->select('efa.email_id', DB::raw("IF(st.last_action_type = 'None', 1, 0) as is_deliverable"),
                        'first_name', 'last_name', 'address', 'address2', 'city', 'state', 'zip', 'country',
                        'gender', 'ip', 'phone', 'source_url', 'dob', 'device_type', 'device_name', 'carrier',
                        'capture_date', 'email_attributable_feed_latest_data.subscribe_date', 'st.last_action_offer_id', DB::raw("DATE(last_action_datetime) as last_action_date"),
                        "other_fields", 'email_attributable_feed_latest_data.created_at', 'efa.updated_at');
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

    public function getAttributionStatus($emailId, $feedId) {
        // either returns Model with properties or null
        return $this->model
                    ->where('email_id', $emailId)
                    ->where('feed_id', $feedId)
                    ->select('attribution_status')
                    ->first();
    }

    public function setAttributionStatus($emailId, $feedId, $status) {
        $this->model
             ->where('email_id', $emailId)
             ->where('feed_id', $feedId)
             ->update(['attribution_status' => $status]);
    }

    public function getCurrentAttributedStatus($emailId) {
        $attrDb = config('database.connections.attribution.database');

        return $this->model
                    ->join("$attrDb.email_feed_assignments as efa", function($join) { 
                        $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                        $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
                    })
                    ->where('efa.email_id', $emailId)
                    ->select('efa.feed_id', 'email_attributable_feed_latest_data.attribution_status')
                    ->first();
    }

    public function addNewRows(array $data) {
        // insert these into the table ... 

        foreach ($data as $row) {
            // need to see if this is third party
            if (3 === (int)$row['party']) {
                $row['other_fields'] = '{}';
                // sensible default
                $row['attribution_status'] = 'POA';
                $this->batchInsert($row);
            }
        }

        $this->insertStored();
    }

    public function getTableName() {
        return config('database.connections.mysql.database') . '.' . $this->model->getTable();
    }

    public function updateRowValues(array $data) {
        // subscribe_date doesn't exist
        foreach ($data as $row) {
            $this->model->update([
                'email_id' => $row['email_id'],
                'feed_id' => $row['feed_id']
            ], [
                'subscribe_date' => $row['subscribe_date']
            ]);
        }
    }

    public function getMinAndMaxIds() {
        $min = $this->model->min('email_id');
        $max = $this->model->max('email_id');
        return [$min, $max];
    }

    public function get($emailId) {
        $attrSchema = config('database.connections.attribution.database');
        return $this->model
                    ->join("$attrSchema.email_feed_assignments as efa", function($join) {
                        $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                        $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
                    })
                    ->join('third_party_email_statuses as tpes', 'email_attributable_feed_latest_data.email_id', '=', 'tpes.email_id')
                    ->whereRaw("efa.email_id = $emailId")
                    ->selectRaw("efa.email_id, IF(tpes.last_action_type = 'None', 1, 0) as is_deliverable, 
                        email_attributable_feed_latest_data.subscribe_date")
                    ->first();
    }

    public function getActionDateDistribution() {
        $attrSchema = config('database.connections.attribution.database');
        $output = [];

        $data = $this->model
            ->join("$attrSchema.email_feed_assignments as efa", function($join) {
                $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
            })
            ->join('third_party_email_statuses as tpes', 'email_attributable_feed_latest_data.email_id', '=', 'tpes.email_id')
            ->selectRaw("date(efa.updated_at) as day, sum(IF(tpes.last_action_type = 'None', 1, 0)) as deliverable_count")
            ->whereRaw("efa.updated_at >= CURDATE() - INTERVAL 3 DAY")
            ->groupBy(DB::raw('date(updated_at)'))
            ->get();

        foreach($data as $row) {
            $output[$row->day] = $row->deliverable_count;
        }

        return $output;
    }

    public function getSubscribeDate($emailId) {
        $attrSchema = config('database.connections.attribution.database');
        $result = $this->model
                    ->join("$attrSchema.email_feed_assignments as efa", function($join) { 
                        $join->on('email_attributable_feed_latest_data.email_id', '=', 'efa.email_id');
                        $join->on('email_attributable_feed_latest_data.feed_id', '=', 'efa.feed_id');
                    })
                    ->where('email_attributable_feed_latest_data.email_id', $emailId)
                    ->select('email_attributable_feed_latest_data.subscribe_date')
                    ->first();

        if ($result) {
            return $result->subscribe_date;
        }
        else {
            return null;
        }
    }

}
