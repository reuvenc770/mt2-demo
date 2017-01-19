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
        $pdo = DB::connection()->getPdo();

        echo "Preparing to insert at " . microtime(true) . PHP_EOL;
        $insertList = [];

        foreach ($massData as $row) {
            $rowString = "("
                . $pdo->quote($row['email_id']) . ',' 
                . $pdo->quote($row['deploy_id']) . ',' 
                . $pdo->quote($row['date']) . ',' 
                . $pdo->quote($row['email_address']) . ',' 
                . $pdo->quote($row['email_domain_id']) . ',' 
                . $pdo->quote($row['email_domain_group_id']) . ',' 
                . $pdo->quote($row['offer_id']) . ',' 
                . $pdo->quote($row['cake_vertical_id']) . ',' 
                . $pdo->quote($row['deliveries']) . ',' 
                . $pdo->quote($row['opens']) . ',' 
                . $pdo->quote($row['clicks']) . ', NOW(), NOW())';

            $insertList[]= $rowString;
        }

        $insertString = implode(',', $insertList);

        DB::connection('list_profile')->insert(
            "INSERT INTO list_profile_flat_table 
            (email_id, deploy_id, date, email_address,
            email_domain_id, email_domain_group_id, offer_id,
            cake_vertical_id, deliveries, opens,
            clicks, created_at, updated_at) VALUES $insertString

            ON DUPLICATE KEY UPDATE
                email_id = email_id,
                deploy_id = deploy_id,
                email_address = email_address,
                email_domain_id = email_domain_id,
                email_domain_group_id = email_domain_group_id,
                offer_id = offer_id,
                cake_vertical_id = cake_vertical_id,
                deliveries = deliveries + VALUES(deliveries),
                opens = opens + VALUES(opens),
                clicks = clicks + VALUES(clicks),
                conversions = conversions,
                created_at = created_at,
                updated_at = NOW()");
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
        return "('{$row->email_id}', '{$row->deploy_id}', '{$row->date}', '{$row->conversions}', NOW(), NOW())";
    }


    private function insertBatchData() {
        if ($this->batchDataSize > 0) {
            $schema = config('database.connections.list_profile.database');

            $inserts = implode(',', $this->batchData);

            DB::statement("INSERT INTO $schema.list_profile_flat_table 
                (email_id, deploy_id, date, conversions, created_at, updated_at)

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
                deliveries = deliveries,
                opens = opens,
                clicks = clicks,
                conversions = VALUES(conversions),
                created_at = created_at,
                updated_at = NOW()");


        }    
    }

    public function extractForS3Upload($startPoint) {
        return $this->flatTable->whereRaw("updated_at > $startPoint");
    }

    public function extractAllForS3() {
        // This will be the current default
        return $this->flatTable->whereRaw("date > CURDATE() - INTERVAL 120 DAY");
    }


    public function mapForS3Upload($row) {
        return [
            'email_id' => $row->email_id,
            'deploy_id' => $row->deploy_id,
            'date' => $row->date,
            'email_address' => $row->email_address,
            'email_domain_id' => $row->email_domain_id,
            'email_domain_group_id' => $row->email_domain_group_id,
            'offer_id' => $row->offer_id,
            'cake_vertical_id' => $row->cake_vertical_id,
            'deliveries' => $row->deliveries,
            'opens' => $row->opens,
            'clicks' => $row->clicks,
            'conversions' => $row->conversions,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at
        ];
    }
}