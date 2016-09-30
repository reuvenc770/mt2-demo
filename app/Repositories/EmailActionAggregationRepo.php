<?php

namespace App\Repositories;

use App\Models\EmailActionAggregation;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class EmailActionAggregationRepo {
  
    private $aggregation;
    private $batchData = [];
    private $batchDataSize;
    const MAX_INSERT_SIZE = 10000;

    public function __construct(EmailActionAggregation $aggregation) {
        $this->aggregation = $aggregation;
        $this->batchDataSize = 0;
    } 

    public function insertBatch($data) {
        if (self::MAX_INSERT_SIZE === $this->batchDataSize) {
            // insert
            $this->insertBatchData();

            // set batchData to data
            $this->batchData = [$this->prepareData($data)];
            $this->batchDataSize = 1;
        }
        else {
            // Merely insert into holding array
            $this->batchData[] = $this->prepareData($data);
            $this->batchDataSize++;
        }
    }


    public function cleanUpBatch() {
        // insert
        $this->insertBatchData();
    }

    private function prepareData($row) {
        return "('{$row->email_id}', '{$row->deploy_id}', 
            '{$row->date}', '{$row->deliveries}', '{$row->opens}', 
            '{$row->clicks}', '{$row->conversions}', NOW(), NOW())";
    }

    private function insertBatchData() {
        if ($this->batchDataSize > 0) {
            $schema = config('database.connections.list_profile.database');

            $inserts = implode(',', $this->batchData);

            DB::statement("INSERT INTO $schema.email_action_aggregations 
                (email_id, deploy_id, date, deliveries, opens, clicks, conversions, created_at, updated_at)

                VALUES $inserts

                ON DUPLICATE KEY UPDATE
                email_id = email_id,
                deploy_id = deploy_id,
                date = date,
                deliveries = VALUES(deliveries),
                opens = VALUES(opens),
                clicks = VALUES(clicks),
                conversions = VALUES(conversions),
                created_at = created_at,
                updated_at = NOW()");
        }    
    }

}