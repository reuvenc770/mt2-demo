<?php

namespace App\Services\SuppressionProcessingStrategies;

use App\Repositories\FirstPartyOnlineSuppressionListRepo;
use App\Services\BrontoReportService;

class RmpSuppressionProcessingStrategy implements ISuppressionProcessingStrategy {

    private $onlineListRepo;
    private $onlineListMap;

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
        $lists = $this->formatLists($supp->lists);

        $this->api->addContact([
            'email' => $supp->email_address, 
            'listIds' => $lists
        ]);
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