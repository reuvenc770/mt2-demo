<?php

namespace App\Repositories;

use App\Models\InvalidEmailInstance;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Repositories\RepoTraits\Batchable;

class InvalidEmailInstanceRepo {
    use Batchable;
  
    private $model;

    public function __construct(InvalidEmailInstance $model) {
        $this->model = $model;
    }

    private function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();

        return '('
            . $pdo->quote($row['feed_id']) . ','
            . $pdo->quote($row['pw']) . ','
            . $pdo->quote($row['email_address']) . ','
            . $pdo->quote($row['source_url']) . ','
            . $pdo->quote($row['capture_date']) . ','
            . $pdo->quote($row['ip']) . ','
            . $pdo->quote($row['first_name']) . ','
            . $pdo->quote($row['last_name']) . ','
            . $pdo->quote($row['address']) . ','
            . $pdo->quote($row['address2']) . ','
            . $pdo->quote($row['city']) . ','
            . $pdo->quote($row['state']) . ','
            . $pdo->quote($row['zip']) . ','
            . $pdo->quote($row['country']) . ','
            . $pdo->quote($row['gender']) . ','
            . $pdo->quote($row['phone']) . ','
            . $pdo->quote($row['dob']) . ','
            . $pdo->quote($row['other_fields']) . ','
            . $pdo->quote($row['posting_string']) . ','
            . $pdo->quote($row['invalid_reason_id']) 
            . ', NOW(), NOW())';

    }

    private function buildBatchedQuery(&$batchData) {
        return "INSERT INTO invalid_email_instances
            (feed_id, pw, email_address, source_url, capture_date, ip, first_name, 
            last_name, address, address2, city, state, zip, country,
            gender, phone, dob, other_fields, posting_string, invalid_reason_id, created_at, updated_at)

            VALUES

            {$batchData}";
    }

}