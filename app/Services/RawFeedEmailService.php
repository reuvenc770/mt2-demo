<?php

namespace App\Services;
use App\Repositories\RawFeedEmailRepo;
use App\Repositories\InvalidEmailInstanceRepo;
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


    public function getMissedRecords($party, $date, $startRawId, $minInvId, $limit) {
        if (3 === $party) {
            $output = [];
            $records = $this->rawRepo->getThirdPartyUnprocessed($startRawId, $date, $minInvId, $limit);

            foreach ($records as $record) {
                $this->maxId = max((int)$record->id, $this->maxId);
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
    public function getPullEmails($feedId,$startdate,$enddate) {
        $records = $this->rawRepo->getPullEmails($feedId,$startdate,$enddate);
        return $records;
    }
    public function getMaxIdPulled() {
        return $this->maxId;
    }

    public function getMinRawIdForDateTime($dateTime) {
        return $this->rawRepo->getMinId($dateTime);
    }

    public function getMinInvalidIdForDate($date) {
        return $this->invalidRepo->getMinIdForDate($date);
    }


}
