<?php

namespace App\Services;

use App\Services\Interfaces\IEtl;
use App\Repositories\EmailActionsRepo;
use App\Repositories\ListProfileFlatTableRepo;
use App\Repositories\CakeConversionRepo;
use App\Repositories\EtlPickupRepo;


class ListProfileActionAggregationService implements IEtl {

    private $actionsRepo;
    private $flatTableRepo;
    private $cakeRepo;
    private $etlPickupRepo;
    const JOB_NAME = 'PopulateListProfileFlatTable';
    
    public function __construct(EmailActionsRepo $actionsRepo, CakeConversionRepo $cakeRepo,  ListProfileFlatTableRepo $flatTableRepo, EtlPickupRepo $etlPickupRepo) {
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
        $conversions = $this->cakeRepo->getConversionsByEmailId();

        $conversions->each(function($data, $id) {
            $this->flatTableRepo->insertBatchConversions($data);
        }, 50000);
        
        $this->flatTableRepo->cleanUpBatchConversions();
    }

    public function load() {}

    private function mapToTable($row) {
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
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at
        ];
    }
}


        