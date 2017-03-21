<?php

namespace App\Services;


use App\Models\Suppression;
use App\Repositories\SuppressionRepo;
use Log;
use App\Repositories\SuppressionGlobalOrangeRepo;
use App\Services\Interfaces\IFeedSuppression;

class GlobalSuppressionService implements IFeedSuppression
{
    protected $repo;

    public function __construct(SuppressionGlobalOrangeRepo $repo) {
        $this->repo = $repo;
    }

    public function checkGlobalSuppression ($emailAddress) {
        return $this->repo->isSuppressed($emailAddress);
    }

    public function returnSuppressedEmails(array $emails) {
        return $this->repo->returnSuppressedEmails($emails);
    }
}
