<?php

namespace App\Services;

use App\Repositories\RawFeedEmailRepo;
use App\Repositories\InvalidEmailInstanceRepo;
use Carbon\Carbon;
use App\DataModels\ProcessingRecord;

class RawFeedEmailService {

    private $rawRepo;
    private $invalidRepo;
    private $maxId = 0;
    
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
            $output = [];
            $records = $this->rawRepo->getThirdPartyUnprocessed($minId, $date, $minInvId);

            foreach ($records as $record) {
                $output[] = new ProcessingRecord($record);
            } 

            return $output;
        }
        elseif (1 === $party) {
            return $this->rawRepo->getFirstPartyUnprocessed($minId, $date, $minInvId, $feedId);
        }
    }

    public function getThirdPartyRecordsWithChars($startPoint, $startChars) {
        $output = [];
        $this->maxId = (int)$startPoint;

        $records = $this->rawRepo->getThirdPartyRecordsWithChars($startPoint, $startChars);

        foreach ($records as $record) {
            $this->maxId = max((int)$record->id, $this->maxId);
            $output[] = new ProcessingRecord($record);
        }

        return $output;
    }

    public function getMaxIdPulled() {
        return $this->maxId;
    }


}