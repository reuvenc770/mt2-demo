<?php

namespace App\Services;

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

    public function insertSuppression($emailAddress, $dateTime, $reasonId) {
        $this->repo->updateOrCreate([
            'email_address' => $emailAddress,
            'suppress_datetime' => $dateTime,
            'reason_id' => $reasonId,
            'type_id' => 0,
            'updated_at' => $dateTime,
            'created_at' => $dateTime
        ]);
    }
}
