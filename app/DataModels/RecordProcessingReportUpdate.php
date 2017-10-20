<?php

namespace App\DataModels;

use App\DataModels\ProcessingRecord;
use App\Models\InvalidReason;

class RecordProcessingReportUpdate {
    
    private $updateArray;

    public function __construct() {
        $this->updateArray = [];
    }


    public function setFields(ProcessingRecord $record) {
        if (!isset($this->updateArray[$record->feedId])) {
            $this->updateArray[$record->feedId] = [];
        }

        if (!isset($this->updateArray[$record->feedId][$record->domainGroupId])) {
            $this->updateArray[$record->feedId][$record->domainGroupId] = [];
        }

        if (!isset($this->updateArray[$record->feedId][$record->domainGroupId][$record->file])) {
            $this->updateArray[$record->feedId][$record->domainGroupId][$record->file] = [
                'totalRecords' => 0,
                'badSourceUrls' => 0,
                'badIpAddresses' => 0,
                'otherInvalid' => 0,
                'suppressed' => 0,
                'suppressedDomains' => 0,
                'phoneCount' => 0,
                'fullPostalCount' => 0,
                'validRecords' => 0,
                'unique' => 0,
                'non-unique' => 0,
                'duplicate' => 0,
                'prev_responder_count' => 0
            ];
        }
    }


    public function incrementTotal(ProcessingRecord $record) {
        $this->updateArray[$record->feedId][$record->domainGroupId][$record->file]['totalRecords']++;
    }


    public function incrementValid(ProcessingRecord $record) {
        $this->updateArray[$record->feedId][$record->domainGroupId][$record->file]['validRecords']++;

        if ('' !== $record->phone) {
            $this->updateArray[$record->feedId][$record->domainGroupId][$record->file]['phoneCount']++;
        }

        if ('' !== $record->address && '' !== $record->zip && '' !== $record->city && '' !== $record->state) {
            $this->updateArray[$record->feedId][$record->domainGroupId][$record->file]['fullPostalCount']++;
        }
    }


    public function incrementInvalid(ProcessingRecord $record, $invalidReasonId) {
        if (InvalidReason::CANADA === $invalidReasonId) {
            $this->updateArray[$record->feedId][$record->domainGroupId][$record->file]['otherInvalid']++;
        }
        elseif (InvalidReason::EMAIL === $invalidReasonId) {
            $this->updateArray[$record->feedId][$record->domainGroupId][$record->file]['otherInvalid']++;
        }
        elseif(InvalidReason::BAD_SOURCE_URL === $invalidReasonId) {
            $this->updateArray[$record->feedId][$record->domainGroupId][$record->file]['badSourceUrls']++;
        }
        elseif(InvalidReason::BAD_IP_ADDRESS === $invalidReasonId) {
            $this->updateArray[$record->feedId][$record->domainGroupId][$record->file]['badIpAddresses']++;
        }
        elseif(InvalidReason::BAD_DOMAIN === $invalidReasonId) {
            $this->updateArray[$record->feedId][$record->domainGroupId][$record->file]['suppressedDomains']++;
        }
        else {
            $this->updateArray[$record->feedId][$record->domainGroupId][$record->file]['otherInvalid']++;
        }
    }


    public function incrementSuppressed(ProcessingRecord $record) {
        $this->updateArray[$record->feedId][$record->domainGroupId][$record->file]['suppressed']++;
    }


    public function incrementUniqueStatus(ProcessingRecord $record) {
        $this->updateArray[$record->feedId][$record->domainGroupId][$record->file][$record->uniqueStatus]++;
    }


    public function incrementPrevResponder(ProcessingRecord $record) {
        $this->updateArray[$record->feedId][$record->domainGroupId][$record->file]['prev_responder_count']++;
    }


    public function toArray() {
        return $this->updateArray;
    }


}