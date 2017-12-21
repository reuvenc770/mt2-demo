<?php

namespace App\Services\SuppressionProcessingStrategies;

use App\Services\Interfaces\ISuppressionProcessingStrategy;
use App\Services\AbstractReportService;
use App\Services\Interfaces\IPostingStrategy;

class FirstPartySuppressionProcessingStrategy implements ISuppressionProcessingStrategy {
    private $apiService;
    private $postingStrategy;
    private $lists = [];

    public function __construct(AbstractReportService $apiService, IPostingStrategy $postingStrategy) {
        $this->apiService = $apiService;
        $this->postingStrategy = $postingStrategy;
    }

    public function setTargets(array $lists) {
        $this->lists = $lists;
    }

    public function processSuppression($emailAddress) {
        $contactInfo = $this->postingStrategy->prepareForSuppressionPosting($emailAddress, [$this->lists]);
        $this->apiService->addContactToLists($contactInfo);
    }

}
