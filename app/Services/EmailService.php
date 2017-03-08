<?php

namespace App\Services;

use App\Repositories\EmailRepo;

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

}