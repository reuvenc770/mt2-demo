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
    private $type;

    const ACTION_TYPE = 'action';
    const CONVERSION_TYPE = 'conversion';

    public function __construct(EmailActionAggregation $aggregation) {
        $this->aggregation = $aggregation;
        $this->batchDataSize = 0;
    }

    public function setType($type) {
        // Strategy pattern probably overkill for this small switch

        if (in_array($type, [self::ACTION_TYPE, self::CONVERSION_TYPE])) {
            $this->type = $type;
        }
        else {
            throw new \Exception("Invalid type $type set for EmailActionAggregationRepo");
        }
    }

    public function insertBatch($data) {

        if (self::MAX_INSERT_SIZE === $this->batchDataSize) {
            $this->insertBatchData();
            // set batchData to new data
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
        $this->insertBatchData();
        $this->batchData = [];
        $this->batchDataSize = 0;
    }


    private function prepareData($row) {
        if (self::ACTION_TYPE === $this->type) {
            return "('{$row->email_id}', '{$row->deploy_id}', 
            '{$row->date}', '{$row->deliveries}', '{$row->opens}', 
            '{$row->clicks}', NOW(), NOW())";
        }
        else {
            return "'{$row->email_id}', '{$row->deploy_id}', '{$row->date}',
            '{$row->conversions}', NOW(), NOW())";
        }
        
    }

    private function insertBatchData() {
        if ($this->batchDataSize > 0) {
            $schema = config('database.connections.list_profile.database');

            $inserts = implode(',', $this->batchData);

            if (self::ACTION_TYPE === $this->type) {
                DB::statement("INSERT INTO $schema.email_action_aggregations 
                    (email_id, deploy_id, date, deliveries, opens, clicks, created_at, updated_at)

                    VALUES $inserts

                    ON DUPLICATE KEY UPDATE
                    email_id = email_id,
                    deploy_id = deploy_id,
                    date = date,
                    deliveries = VALUES(deliveries),
                    opens = VALUES(opens),
                    clicks = VALUES(clicks),
                    conversions = conversions,
                    created_at = created_at,
                    updated_at = NOW()");
            }
            else {
                DB::statement("INSERT INTO $schema.email_action_aggregations 
                    (email_id, deploy_id, date, conversions, created_at, updated_at)

                    VALUES $inserts

                    ON DUPLICATE KEY UPDATE
                    email_id = email_id,
                    deploy_id = deploy_id,
                    date = date,
                    deliveries = deliveries,
                    opens = opens,
                    clicks = clicks,
                    conversions = VALUES(conversions),
                    created_at = created_at,
                    updated_at = NOW()");
            }


        }    
    }

}
