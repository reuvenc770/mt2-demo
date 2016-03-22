<?php

namespace App\Services;

use App\Repositories\EmailRecordRepo;
use Log;

class EmailRecordService {
    CONST OPENER = 1;
    CONST CLICKER = 2;
    CONST CONVERTER = 3;
    CONST DELIVERABLE = 4; 

    protected $repo;

    public function __construct ( EmailRecordRepo $repo ) {
        $this->repo = $repo;
    }

    public function getEmailId ( $email ) {
        return $this->repo->getEmailId( $email );
    }

    public function recordDeliverable ( $recordType , $email , $espId , $campaignId , $date ) {
        if ( $this->repo->isValidActionType( $recordType ) ) {
            return $this->repo->recordDeliverable( $recordType , $email , $espId , $campaignId , $date );
        } else {
            Log::error( "Record Type '{$recordType}' is not valid." );
            return false;
        }
    }
}
