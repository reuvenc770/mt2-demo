<?php

namespace App\Services\SuppressionProcessingStrategies;

use App\Repositories\FirstPartyOnlineSuppressionListRepo;
use App\Services\BrontoReportService;

class SimplyJobsSuppressionProcessingStrategy implements ISuppressionProcessingStrategy {

    private $onlineListRepo;
    private $onlineListMap;
    private $apiService;

    public function __construct(FirstPartyOnlineSuppressionListRepo $onlineListRepo, BrontoReportService $apiService) {
        $this->onlineListRepo = $onlineListRepo;
        $this->apiService = $apiService;
    }

    public function setFeedId($feedId) {
        $result = $this->onlineListRepo->getForFeedId($feedId);

        foreach ($result as $row) {
            $this->onlineListMap[$row->suppression_list_id] = $row->target_list;
        }
    }

    public function processSuppression($supp) {
        $lists = $this->formatLists($supp->suppression_lists);

        // maybe this isn't enough?
        $this->apiService->addContact([
            'email' => $supp->email_address, 
            'listIds' => $lists
        ]);

        /**
            Need to set email template stuff here
        */

    }

    private function formatLists($listString) {
        $listArray = explode(',', $listString);
        $output = [];

        foreach ($listArray as $list) {
            if (isset($this->onlineListMap[$list])) {
                $output[] = $this->onlineListMap[$list];
            }
        }

        return $output;
    }
}