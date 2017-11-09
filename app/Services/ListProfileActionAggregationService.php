<?php

namespace App\Services;

use App\Services\Interfaces\IEtl;
use App\Repositories\EmailActionsRepo;
use App\Repositories\ListProfileFlatTableRepo;
use App\Repositories\TrackingRepo;
use App\Repositories\EtlPickupRepo;
use DB;
use Carbon\Carbon;

class ListProfileActionAggregationService implements IEtl {

    private $actionsRepo;
    private $flatTableRepo;
    private $cakeRepo;
    private $etlPickupRepo;
    const JOB_NAME = 'PopulateListProfileFlatTable';
    const CAKE_LIMIT = 50000;
    const TRACKING_DAYS_BACK = 5;
    
    public function __construct(EmailActionsRepo $actionsRepo, TrackingRepo $cakeRepo,  ListProfileFlatTableRepo $flatTableRepo, EtlPickupRepo $etlPickupRepo) {
        $this->actionsRepo = $actionsRepo;
        $this->flatTableRepo = $flatTableRepo;
        $this->cakeRepo = $cakeRepo;
        $this->etlPickupRepo = $etlPickupRepo;
    }

    public function extract($lookback = null) {
        // Part 1: Actions

        $startPoint = $this->etlPickupRepo->getLastInsertedForName(self::JOB_NAME);
        $endPoint = $this->actionsRepo->maxId();

        while ($startPoint < $endPoint) {
            
            // limit of ~10k rows to prevent memory allocation issues and maximize bulk inserts
            $limit = 10000;
            $segmentEnd = $this->actionsRepo->nextNRows($startPoint, $limit);

            // If we've overshot, $segmentEnd will be null
            $segmentEnd = $segmentEnd ? $segmentEnd : $endPoint;

            echo "Starting " . self::JOB_NAME . " collection at row $startPoint, ending at $segmentEnd" . PHP_EOL;
            $data = $this->actionsRepo->pullAggregatedListProfileActions($startPoint, $segmentEnd);

            if ($data) {
                // perform mass insert
                $insertData = [];
                foreach ($data as $row) {
                    $insertData[] = $this->mapToTable($row);
                }

                $this->flatTableRepo->massInsertActions($insertData);
                $startPoint = $segmentEnd;
            }
            else {
                // if no data received, try try again
                echo "No data received" . PHP_EOL;
                $startPoint = $segmentEnd;
                continue;
            }
        }

        $this->etlPickupRepo->updatePosition(self::JOB_NAME, $endPoint);

        // Part 2: Conversions
        $startPoint = Carbon::today()->subDays(self::TRACKING_DAYS_BACK);
        $startEmailId = 0;

        $conversions = $this->cakeRepo->getEmailSortedCakeActions($startPoint, $startEmailId, self::CAKE_LIMIT);

        while (count($conversions) > 0) {
            foreach ($conversions as $conv) {
                $this->flatTableRepo->insertBatchConversions($conv);
                $startEmailId = $conv->email_id;
            }

            $conversions = $this->cakeRepo->getEmailSortedCakeActions($startPoint, $startEmailId, self::CAKE_LIMIT);
        }
        
        $this->flatTableRepo->cleanUpBatchConversions();
    }

    public function load() {}

    private function mapToTable($row) {
        $pdo = DB::connection()->getPdo();
        return "("
            . $pdo->quote($row->email_id) . ',' 
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
            . $pdo->quote($row->has_open) . ','
            . $pdo->quote($row->has_esp_click) . ','
            . $pdo->quote($row->has_click) . ','
            . $pdo->quote($row->deliveries) . ',' 
            . $pdo->quote($row->opens) . ',' 
            . $pdo->quote($row->clicks) . ','
            . $pdo->quote($row->party) . ', NOW(), NOW())';
    }
}


        