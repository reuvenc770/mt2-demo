<?php

namespace App\Repositories;

use App\Models\RecordData;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class RecordDataRepo {

    private $model;
    private $batchData = [];
    private $batchDataCount = 0;
    const INSERT_THRESHOLD = 10000;

    public function __construct(RecordData $model) {
        $this->model = $model;
    }

    public function insert($row) {
        if ($this->batchDataCount >= self::INSERT_THRESHOLD) {

            $this->batchData = implode(', ', $this->batchData);

            DB::statement("
                INSERT INTO record_data (email_id, first_name, last_name, 
                    address, address2, city, state, zip, country, gender, 
                    ip, phone, source_url, dob, capture_date, other_fields)

                VALUES 

                {$this->batchData}

                ON DUPLICATE KEY UPDATE
                email_id = email_id,
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
                other_fields = VALUES(other_fields)
            ");
            
            $this->batchData = [$this->transformRowToString($row)];
            $this->batchDataCount = 1;
        }
        else {
            $this->batchData[] = $this->transformRowToString($row);
            $this->batchDataCount++;
        }
    }

    public function insertStored() {
        $this->batchData = implode(', ', $this->batchData);

        DB::statement("
            INSERT INTO record_data (email_id, first_name, last_name, 
                address, address2, city, state, zip, country, gender, 
                ip, phone, source_url, dob, capture_date, other_fields)

            VALUES 

            {$this->batchData}

            ON DUPLICATE KEY UPDATE
            email_id = email_id,
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
            other_fields = VALUES(other_fields)
        ");

        $this->batchData = [];
        $this->batchDataCount = 0;
    }

    private function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();

        return '('
            . $pdo->quote($row['email_id']) . ','
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
            . $pdo->quote($row['capture_date']) . ','
            . "'{}'" // other fields empty for now
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
                other_fields = other_fields
            ");
        }

    }
}