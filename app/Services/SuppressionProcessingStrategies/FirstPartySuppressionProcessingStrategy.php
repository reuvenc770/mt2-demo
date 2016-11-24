<?php

namespace App\Services\SuppressionProcessingStrategies;

use App\Repositories\FirstPartyOnlineSuppressionListRepo;
use App\Services\AbstractReportService;

class FirstPartySuppressionProcessingStrategy implements ISuppressionProcessingStrategy {

    private $onlineListRepo;
    private $onlineListMap;
    private $apiService;

    public function __construct(FirstPartyOnlineSuppressionListRepo $onlineListRepo, AbstractReportService $apiService) {
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
        $this->apiService->addContactToLists($supp->email_address, $lists);

        /**
            Need to set email template stuff here for SimplyJobs
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