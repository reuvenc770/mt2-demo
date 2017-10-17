<?php

namespace App\Services;

use App\Repositories\EmailRepo;
use App\DataModels\ProcessingRecord;

class EmailService {

    private $repo;

    public function __construct(EmailRepo $repo) {
        $this->repo = $repo;
    }

    public function getId($emailAddress) {
        return $this->repo->getEmailId($emailAddress);
    }

    public function getEmailAddress($id) {
        return $this->repo->getEmailAddress($id);
    }

    public function getRecordInfo($identifier, $type) {
        if ('email' === $type) {
            return $this->repo->getRecordInfoAddress($identifier);
        }
        else {
            return $this->repo->getRecordInfoId($identifier);
        }
    }

    public function createFromRecord(ProcessingRecord $record) {
        // Didn't exist at record list generation time and not suppressed (yet)
        // We might run into issues due to the separate processing of data from feeds of different parties
        $record->newEmail = true;
        $email = $this->repo->insertNew($record->mapToEmails());
        $record->emailId = $email->id;

        return $record;
    }

}