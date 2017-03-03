<?php

namespace App\Repositories;

use App\Models\ListProfileFlatTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Repositories\RepoInterfaces\IAwsRepo;

/**
 *
 */
class ListProfileFlatTableRepo implements IAwsRepo {
  
    private $flatTable;
    private $batchData = [];
    private $batchDataSize;
    const MAX_INSERT_SIZE = 10000;

    public function __construct(ListProfileFlatTable $flatTable) {
        $this->flatTable = $flatTable;
    } 

    public function massInsertActions($massData) {
        echo "Preparing to insert at " . microtime(true) . PHP_EOL;
        $insertList = [];

        $insertString = implode(',', $massData);

        DB::connection('list_profile')->insert(
            "INSERT INTO list_profile_flat_table 
            (email_id, deploy_id, esp_account_id, date, email_address,
            lower_case_md5, upper_case_md5, email_domain_id, 
            email_domain_group_id, offer_id, cake_vertical_id, has_esp_open, 
            has_open, has_esp_click, has_click, deliveries, opens,
            clicks, created_at, updated_at) VALUES $insertString

            ON DUPLICATE KEY UPDATE
                email_id = email_id,
                deploy_id = deploy_id,
                email_address = email_address,
                email_domain_id = email_domain_id,
                email_domain_group_id = email_domain_group_id,
                offer_id = offer_id,
                cake_vertical_id = cake_vertical_id,
                has_esp_open = IF(VALUES(has_esp_open) > 0, VALUES(has_esp_open), has_esp_open),
                has_open = IF(VALUES(has_esp_open) > 0 OR has_cs_open > 0, 1, 0),
                has_esp_click = IF(VALUES(has_esp_click) > 0, VALUES(has_esp_click), has_esp_click),
                has_click = IF(VALUES(has_esp_click) > 0 OR has_cs_click > 0 OR has_tracking_click > 0, 1, 0),
                deliveries = deliveries + VALUES(deliveries),
                opens = opens + VALUES(opens),
                clicks = clicks + VALUES(clicks),
                conversions = conversions,
                created_at = created_at,
                updated_at = VALUES(updated_at)");
    }

    public function insertBatchConversions($data) {

        if (self::MAX_INSERT_SIZE === $this->batchDataSize) {
            $this->insertBatchData();
            // set batchData to new data
            $this->batchData = [$this->prepareConversionData($data)];
            $this->batchDataSize = 1;
        }
        else {
            // Merely insert into holding array
            $this->batchData[] = $this->prepareConversionData($data);
            $this->batchDataSize++;
        }
    }


    public function cleanUpBatchConversions() {
        $this->insertBatchData();
        $this->batchData = [];
        $this->batchDataSize = 0;
    }

    private function prepareConversionData($row) {
        $conversionFlag = ((int)$row->conversions) > 0 ? 1 : 0;
        return "('{$row->email_id}', '{$row->deploy_id}', '{$row->date}', '$conversionFlag', '$conversionFlag', '{$row->conversions}', NOW(), NOW())";
    }


    private function insertBatchData() {
        if ($this->batchDataSize > 0) {
            $schema = config('database.connections.list_profile.database');

            $inserts = implode(',', $this->batchData);

            DB::statement("INSERT INTO $schema.list_profile_flat_table 
                (email_id, deploy_id, date, has_tracking_conversion, has_conversion, conversions, created_at, updated_at)

                VALUES $inserts

                ON DUPLICATE KEY UPDATE
                email_id = email_id,
                deploy_id = deploy_id,
                date = date,
                email_address = email_address,
                email_domain_id = email_domain_id,
                email_domain_group_id = email_domain_group_id,
                offer_id = offer_id,
                cake_vertical_id = cake_vertical_id,
                has_tracking_conversion = IF(VALUES(has_tracking_conversion) > 0, VALUES(has_tracking_conversion), has_tracking_conversion),
                has_conversion = IF(VALUES(has_conversion) > 0, VALUES(has_conversion), has_conversion),
                deliveries = deliveries,
                opens = opens,
                clicks = clicks, # Clicks we should have as well ... 
                conversions = VALUES(conversions),
                created_at = created_at,
                updated_at = NOW()");


        }    
    }

    public function massInsertContentServerActions($data) {
        if (sizeof($data) > 0) {
            $schema = config('database.connections.list_profile.database');
            $inserts = implode(',', $data);

            DB::statement("INSERT INTO $schema.list_profile_flat_table
            (email_id, deploy_id, date, email_address, lower_case_md5, upper_case_md5, 
                email_domain_id, has_cs_open, has_open, has_cs_click, has_click)

            VALUES $inserts

            ON DUPLICATE KEY UPDATE
            email_id = email_id,
            deploy_id = deploy_id,
            date = date,
            email_address = VALUES(email_address),
            lower_case_md5 = VALUES(lower_case_md5),
            upper_case_md5 = VALUES(upper_case_md5),
            email_domain_id = email_domain_id,
            has_cs_open = IF(VALUES(has_cs_open) > 0, 1, 0),
            has_open = IF(VALUES(has_cs_open) > 0 OR has_esp_open > 0, 1, 0),
            has_cs_click = IF(VALUES(has_cs_click) > 0, 1, 0),
            has_click = IF(VALUES(has_cs_click) > 0 OR has_esp_click > 0, 1, 0),
            updated_at = NOW()");
        }
    }

    public function extractForS3Upload($startPoint) {
        return $this->flatTable->whereRaw("updated_at > $startPoint");
    }

    public function extractAllForS3() {
        // This will be the current default
        return $this->flatTable->whereRaw("date > CURDATE() - INTERVAL 10 DAY");
    }


    public function mapForS3Upload($row) {
        $pdo = DB::connection('redshift')->getPdo();
        return $pdo->quote($row->email_id) . ','
            . $pdo->quote($row->deploy_id) . ','
            . $pdo->quote($row->esp_account_id) . ','
            . $pdo->quote($row->date) . ','
            . $pdo->quote($row->email_address) . ','
            . $pdo->quote($row->lower_case_md5) . ','
            . $pdo->quote($row->upper_case_md5) . ','
            . $pdo->quote($row->email_domain_id) . ','
            . $pdo->quote($row->email_domain_group_id) . ','
            . $pdo->quote($row->offer_id) . ','
            . $pdo->quote($row->cake_vertical_id) . ','
            . $pdo->quote($row->has_esp_open) . ','
            . $pdo->quote($row->has_cs_open) . ','
            . $pdo->quote($row->has_open) . ','
            . $pdo->quote($row->has_esp_click) . ','
            . $pdo->quote($row->has_cs_click) . ','
            . $pdo->quote($row->has_tracking_click) . ','
            . $pdo->quote($row->has_click) . ','
            . $pdo->quote($row->has_tracking_conversion) . ','
            . $pdo->quote($row->has_conversion) . ','
            . $pdo->quote($row->deliveries) . ','
            . $pdo->quote($row->opens) . ','
            . $pdo->quote($row->clicks) . ','
            . $pdo->quote($row->conversions) . ','
            . $pdo->quote($row->created_at) . ','
            . $pdo->quote($row->updated_at);
    }

    public function getConnection() {
        return $this->flatTable->getConnectionName();
    }

    public function getDeploysOnDate($date) {
        $output = [];

        $deploys = $this->flatTable
                    ->where('date', $date)
                    ->selectRaw("DISTINCT(deploy_id) as deploy_id")
                    ->get()->toArray();

        foreach($deploys as $deployArr) {
            $output[] = (int)$deployArr['deploy_id'];
        }

        return $output;
    }

}