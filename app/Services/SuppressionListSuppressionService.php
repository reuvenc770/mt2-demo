<?php

namespace App\Services;

use App\Repositories\SuppressionListSuppressionRepo;
use App\Services\Interfaces\IFeedSuppression;

class SuppressionListSuppressionService implements IFeedSuppression {

    private $repo;
    private $listId;

    public function __construct(SuppressionListSuppressionRepo $repo) {
        $this->repo = $repo;
    }

    public function setListId($listId) {
        $this->listId = $listId;
    }

    public function returnSuppressedEmails(array $emails) {
        return $this->repo->returnSuppressedWithFeedIds($emails, [$this->listId]);
    }
}
