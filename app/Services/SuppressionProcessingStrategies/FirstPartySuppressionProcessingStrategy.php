<?php

namespace App\Services\SuppressionProcessingStrategies;

use App\Services\Interfaces\ISuppressionProcessingStrategy;
use App\Repositories\FirstPartyOnlineSuppressionListRepo;
use App\Services\AbstractReportService;

class FirstPartySuppressionProcessingStrategy implements ISuppressionProcessingStrategy {

    private $onlineListRepo;
    private $onlineListMap;
    private $apiService;
    private $postingStrategy;
    private $suppressionListIds = [];

    public function __construct(FirstPartyOnlineSuppressionListRepo $onlineListRepo, AbstractReportService $apiService, IPostingStrategy $postingStrategy) {
        $this->onlineListRepo = $onlineListRepo;
        $this->apiService = $apiService;
        $this->postingStrategy = $postingStrategy;
    }

    public function setSuppressionListIds(array $suppressionListIds) {
        $this->suppressionListIds = $suppressionListIds;
    }

    public function setFeedId($feedId) {
        $result = $this->onlineListRepo->getForFeedId($feedId);

        foreach ($result as $row) {
            $this->onlineListMap[$row->suppression_list_id] = $row->target_list;
        }
    }

    public function processSuppression($emailAddress) {
        $contactInfo = $this->formatLists($emailAddress, $this->suppressionListIds);
        $this->apiService->addContactToLists($contactInfo);
    }

    private function formatLists($emailAddress, $listString) {
        $listArray = explode(',', $listString);
        $targetIds = [];

        foreach ($listArray as $list) {
            if (isset($this->onlineListMap[$list])) {
                $targetIds[] = $this->onlineListMap[$list];
            }
        }

        return $this->postingStrategy->prepareForSuppressionPosting($emailAddress, $targetIds);
    }
}