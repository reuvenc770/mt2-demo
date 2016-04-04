<?php

namespace App\Services;

use App\Repositories\EmailRecordRepo;
use Log;

class EmailRecordService {
    protected $repo;
    protected $records = [];

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

    public function clearRecordList () {
        $this->records = [];
    }

    public function queueDeliverable ( $recordType , $email , $espId , $campaignId , $date ) {
        if ( $this->repo->isValidActionType( $recordType ) ) {
            $this->records []= [
                'recordType' => $recordType ,
                'email' => $email ,
                'espId' => $espId ,
                'campaignId' => $campaignId ,
                'date' => $date
            ];
        } else {
            Log::error( "Record Type '{$recordType}' is not valid." );
            return false;
        }
    }

    public function massRecordDeliverables () {
        $this->repo->massRecordDelierables( $this->records );
    }
}
