<?php

namespace App\Services;

use App\Repositories\SuppressionListSuppressionRepo;
use App\Repositories\FirstPartyOnlineSuppressionListRepo;
use App\Services\Interfaces\IFeedSuppression;

class SuppressionListSuppressionService implements IFeedSuppression {

    private $repo;
    private $listRepo;
    private $feedId;

    public function __construct(SuppressionListSuppressionRepo $repo, FirstPartyOnlineSuppressionListRepo $listRepo) {
        $this->repo = $repo;
        $this->listRepo = $listRepo;
    }

    public function setFeedId($feedId) {
        $this->feedId = $feedId;
    }

    public function returnSuppressedEmails(array $emails) {
        $listIds = $this->listRepo->getListsForFeed($this->feedId);
        return $this->repo->returnSuppressedWithFeedIds($emails, $listIds);
    }
}
