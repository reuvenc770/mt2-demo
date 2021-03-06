<?php

namespace App\Services;
use App\Repositories\EmailCampaignStatisticRepo;
use App\Repositories\EmailActionsRepo;
use App\Repositories\EtlPickupRepo;
use App\Services\AbstractEtlService;
use PDO;

class EmailCampaignAggregationService extends AbstractEtlService {

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

    public function extract($lookback = null) {
        $startPoint = $this->etlPickupRepo->getLastInsertedForName(self::JOB_NAME);
        $endPoint = $this->actionsRepo->maxId();

        while ($startPoint < $endPoint) {
            
            // limit of ~10k rows to prevent memory allocation issues and maximize bulk inserts
            $limit = 10000;
            $segmentEnd = $this->actionsRepo->nextNRows($startPoint, $limit);

            // If we've overshot, $segmentEnd will be null
            $segmentEnd = $segmentEnd ? $segmentEnd : $endPoint;

            echo "Starting " . self::JOB_NAME . " collection at row $startPoint, ending at $segmentEnd" . PHP_EOL;
            $data = $this->actionsRepo->pullAggregatedReportActions($startPoint, $segmentEnd);

            if ($data) {
                // perform mass insert
                $insertData = [];
                foreach ($data as $row) {
                    $insertData[] = $this->mapToEmailCampaignsTable($row);
                }

                $this->statsRepo->massInsertActions($insertData);
                $startPoint = $segmentEnd;
            }
            else {
                // if no data received
                echo "No data received" . PHP_EOL;
                $startPoint = $segmentEnd;
                continue;
            }
        }

        $this->etlPickupRepo->updatePosition(self::JOB_NAME, $endPoint);
    }

    public function load() {}

    private function mapToEmailCampaignsTable($row) {

        // check if open actions are done subsequent to that?
        $firstSectionOpen = $this->getFirstItem($row->esp_first_open_datetimes, null);
        $lastSectionOpen = $this->getFirstItem($row->esp_last_open_datetimes, null);

        $firstSectionClick = $this->getFirstItem($row->esp_first_click_datetimes, null);
        $lastSectionClick = $this->getFirstItem($row->esp_last_click_datetimes, null);

        if (null === $firstSectionOpen) {
            $firstSectionOpen = $firstSectionClick;
        }

        return [
            'email_id' => $row->email_id,
            'campaign_id' => $row->deploy_id,
            'last_status' => $this->getFirstItem($row->statuses, ''),
            'esp_first_open_datetime' => $firstSectionOpen,
            'esp_last_open_datetime' => $lastSectionOpen,
            'esp_total_opens' => $row->opens_counted,
            'esp_first_click_datetime' => $firstSectionClick,
            'esp_last_click_datetime' => $lastSectionClick,
            'esp_total_clicks' => $row->clicks_counted,
            'unsubscribed' => ((int)$row->unsubscribed > 0 ? 1 : 0)
        ];
    }


    private function getFirstItem($string, $default) {
        $array = explode(',', $string);
        if (sizeof($array) > 0) {
            return $array[0];
        }
        else {
            return $default;
        }
    }
    
}