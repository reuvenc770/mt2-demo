<?php

namespace App\Services;
use App\Repositories\EmailCampaignStatisticRepo;
use App\Repositories\EmailActionsRepo;
use App\Repositories\EtlPickupRepo;
use PDO;

class EmailCampaignAggregationService {

    private $statsRepo;
    private $actionsRepo;
    private $etlPickupRepo;
    private $actionMap;
    const JOB_NAME = "PopulateEmailCampaignStats";

    public function __construct(EmailCampaignStatisticRepo $statsRepo, EmailActionsRepo $actionsRepo, EtlPickupRepo $etlPickupRepo, $actionMap) {
        $this->statsRepo = $statsRepo;
        $this->etlPickupRepo = $etlPickupRepo;
        $this->actionsRepo = $actionsRepo;
        $this->actionMap = $actionMap;
    }

    public function run() {

        $startPoint = $this->etlPickupRepo->getLastInsertedForName(self::JOB_NAME);
        $endPoint = $this->actionsRepo->maxId();

        while ($startPoint < $endPoint) {
            echo "Starting {self::JOB_NAME} collection at row $startPoint" . PHP_EOL;
            
            // limit of ~10k rows to prevent memory allocation issues and maximize bulk inserts
            $limit = 10000;
            $data = $this->actionsRepo->pullLimitedActionsInLast($startPoint, $limit);
            #$data = $this->actionsRepo->pullAggregatedActions($startPoint, $limit);

            // perform mass insert
            if ($data) {
                $insertData = [];
                foreach ($data as $row) {
                    $actionType = $this->actionMap[$row['action_id']];
                    
                    // get the last id by row - but only if it's a maximum
                    #$startPoint = (int)$row['max_id'] > $startPoint ? (int)$row['max_id'] : $startPoint;
                    $startPoint = (int)$row['id'];
                    $this->statsRepo->insertOrUpdate($row, $actionType);
                    #$insertData[] = $this->mapToEmailCampaignsTable($row);
                }

                #$this->statsRepo->massInsertActions($insertData);
            }
        }

        $this->etlPickupRepo->updatePosition(self::JOB_NAME, $endPoint);
        echo "done with email actions" . PHP_EOL;
    }

    private function mapToEmailCampaignsTable($row) {

        // check if 
        $firstSectionOpen = $this->getFirstItem($row['esp_first_open_datetimes']);
        $lastSectionOpen = $this->getFirstItem($row['esp_last_open_datetimes']);

        $firstSectionClick = $this->getFirstItem($row['esp_first_click_datetimes']);
        $lastSectionClick = $this->getFirstItem($row['esp_last_click_datetimes']);

        return [
            'email_id' => $row['email_id'],
            'campaign_id' => $row['campaign_id'],
            'last_status' => $this->getFirstItem($row['statuses']),
            'esp_first_open_datetime' => $firstSectionOpen,
            'esp_last_open_datetime' => $lastSectionOpen,
            'esp_total_opens' => $row['opens_counted'],
            'esp_first_click_datetime' => $firstSectionClick,
            'esp_last_click_datetime' => $lastSectionClick,
            'esp_total_clicks' => $row['clicks_counted']
        ];
    }

    private function getFirstItem($string) {
        $array = explode(',', $string);
        if (sizeof($array) > 0) {
            return $array[0];
        }
        else {
            return '';
        }
    }
    
}