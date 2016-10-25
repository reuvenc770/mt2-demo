<?php

namespace App\Services;

use App\Events\NewActions;
use App\Models\ActionType;
use App\Models\EmailAction;
use App\Repositories\EmailRecordRepo;
use Log;
use Carbon\Carbon;
use DB;

class EmailRecordService {
    protected $repo;
    protected $records = [];
    const MAX_RECORD_COUNT = 50000;

    public function __construct ( EmailRecordRepo $repo ) {
        $this->repo = $repo;
    }

    public function getEmailId ( $email ) {
        return $this->repo->getEmailId( $email );
    }

    public function getEmailAddress ( $eid ) {
        return $this->repo->getEmailAddress( $eid );
    }

    public function recordDeliverable ( $recordType , $email , $espId , $deployId, $espInternalId , $date ) {
        if ( $this->repo->isValidActionType( $recordType ) ) {
            return $this->repo->recordDeliverable( $recordType , $email , $espId , $deployId, $espInternalId , $date );
        } else {
            Log::error( "Record Type '{$recordType}' is not valid." );
            return false;
        }
    }

    public function clearRecordList () {
        $this->records = [];
    }

    public function queueDeliverable ( $recordType , $email , $espId , $deployId, $espInternalId , $date ) {
        if ( $this->repo->isValidActionType( $recordType ) ) {
            $this->records []= [
                    'recordType' => $recordType ,
                    'email' => $email ,
                    'deployId' => $deployId,
                    'espId' => $espId ,
                    'espInternalId' => $espInternalId ,
                    'date' => $date
                ];

            if (self::MAX_RECORD_COUNT <= sizeof($this->records)) {
                $this->massRecordDeliverables();
            }
            
        } else {
            Log::error( "Record Type '{$recordType}' is not valid." );
            return false;
        }
    }

    public function massRecordDeliverables () {
        $count = count($this->records);

        try {
            $this->repo->massRecordDeliverables($this->records);


        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $this->records = []; // clear out to free up space
        }
        return $count;
    }

    public function withinTwoDays($espId, $campaignId){
        return $this->repo->withinTwoDays($espId, $campaignId);
    }
}
