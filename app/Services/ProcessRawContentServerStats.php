<?php

namespace App\Services;

use App\Repositories\ContentServerStatsRawRepo;
use App\Repositories\ListProfileFlatTableRepo;
use App\Repositories\LinkRepo;
use App\Repositories\EtlPickupRepo;
use Log;
use DB;

class ProcessRawContentServerStats {
    
    private $csRepo;
    private $lpRepo;
    private $pickupRepo;
    private $linkRepo;
    private $jobName;
    const LIMIT = 10000;

    public function __construct(ContentServerStatsRawRepo $csRepo, ListProfileFlatTableRepo $lpRepo, LinkRepo $linkRepo, EtlPickupRepo $pickupRepo) {
        $this->csRepo = $csRepo;
        $this->lpRepo = $lpRepo;
        $this->linkRepo = $linkRepo;
        $this->pickupRepo = $pickupRepo;
    }

    public function extract($startPoint) {
        $endPoint = $this->csRepo->maxId();

        while ($startPoint < $endPoint) {
            $segmentEnd = $this->csRepo->nextNRows($startPoint, self::LIMIT);

            // If we've overshot, $segmentEnd will be null
            $segmentEnd = $segmentEnd ? $segmentEnd : $endPoint;

            echo "Starting " . $this->jobName . " collection at row $startPoint, ending at $segmentEnd" . PHP_EOL;
            $data = $this->csRepo->pullAggregatedActions($startPoint, $segmentEnd);

            if ($data) {
                // perform mass insert
                $insertData = [];
                foreach ($data as $row) {
                    if ($row->link_id > 0) {
                        $deployId = $this->linkRepo->getDeployIdFromLink($row->link_id);
                    }
                    else {
                        $deployId = $row->deploy_id;
                    }

                    if ($deployId) {
                        $insertData[] = $this->mapToTable($row, $deployId);
                    }
                    else {
                        // Deploy id could not be found from link
                        Log::warning("Deploy id could not be found for " . $row->link_id);
                    }
                }

                $this->lpRepo->massInsertContentServerActions($insertData);
                $startPoint = $segmentEnd;
            }
            else {
                // if no data received, try try again
                echo "No data received" . PHP_EOL;
                $startPoint = $segmentEnd;
                continue;
            }
        }

        $this->pickupRepo->updatePosition($this->jobName, $endPoint);
    }

    // Empty becaues all logic is contained within
    public function load() {}

    public function setJobName($jobName) {
        $this->jobName = $jobName;
    }

    private function mapToTable($row, $deployId) {
        $pdo = DB::connection()->getPdo();

        return '('
            . $row->email_id . ','
            . $deployId . ','
            . $pdo->quote($row->date) . ','
            . $pdo->quote($row->email_address) . ','
            . $pdo->quote($row->lower_case_md5) . ','
            . $pdo->quote($row->upper_case_md5) . ','
            . $pdo->quote($row->email_domain_id) . ','
            . $row->has_cs_open . ','
            . $row->has_cs_open . ',' # has_open
            . $row->has_cs_click . ','
            . $row->has_cs_click . ')'; # has_click
    }

}