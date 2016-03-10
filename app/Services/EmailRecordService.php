<?php

namespace App\Services;

use App\Repositories\EmailRecordRepo;

class EmailRecordService {
    protected $repo;

    public function __construct ( EmailRecordRepo $repo ) {
        $this->repo = $repo;
    }

    public function getEmailId ( $email ) {
        return $this->repo->getEmailId( $email );
    }

    public function recordOpen ( $emailId , $espId , $campaignId , $date ) {
        $this->repo->recordOpen( $emailId , $espId , $campaignId , $date );
    }

    public function recordClick ( $emailId , $espId , $campaignId , $date ) {
        $this->repo->recordClick( $emailId , $espId , $campaignId , $date );
    }

    public function recordDeliverable ( $emailId , $espId , $campaignId , $date ) {
        $this->repo->recordDeliverable( $emailId , $espId , $campaignId , $date );
    }
}
