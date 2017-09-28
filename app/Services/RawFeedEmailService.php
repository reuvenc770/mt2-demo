<?php

namespace App\Services;

use App\Repositories\RawFeedEmailRepo;
use App\Repositories\InvalidEmailInstanceRepo;
use Carbon\Carbon;

class RawFeedEmailService {

    private $rawRepo;
    private $invalidRepo;
    
    public function __construct(RawFeedEmailRepo $rawRepo,
    InvalidEmailInstanceRepo $invalidRepo) {

        $this->rawRepo = $rawRepo;
        $this->invalidRepo = $invalidRepo;
    }


    public function getMissedRecords($party, $hoursBack) {

        $date = Carbon::today()->toDateString();
        $start = Carbon::now()->subHours($hoursBack)->toDateTimeString();

        $minId = $this->rawRepo->getMinId($start);
        $minInvId = $this->invalidRepo->getMinIdForDate($date);

        if (3 === $party) {
            return $this->rawRepo->getThirdPartyUnprocessed($minId, $date, $minInvId);
        }
        elseif (1 === $party) {
            return $this->rawRepo->getFirstPartyUnprocessed($minId, $date, $minInvId, $feedId);
        }
    }


}