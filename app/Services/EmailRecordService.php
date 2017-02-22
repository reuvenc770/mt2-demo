<?php

namespace App\Services;

use App\Events\NewActions;
use App\Models\ActionType;
use App\Models\EmailAction;
use App\Repositories\EmailRecordRepo;
use Log;
use Carbon\Carbon;
use DB;
use App\Jobs\SetSchedulesJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class EmailRecordService {
    use DispatchesJobs;

    protected $repo;
    protected $records = [];
    protected $newActions = [];

    protected $firstEspId = 0;

    const MAX_RECORD_COUNT = 50000;
    const QUEUE = 'filters';

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
            $this->firstEspId = $espId;

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

    public function massRecordDeliverables ( $fireNewActionsJob = false ) {
        $count = count($this->records);

        try {
            $this->repo->massRecordDeliverables($this->records);


        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $this->newActions = array_merge( $this->newActions , $this->records );
            $this->records = []; // clear out to free up space
        }

        if ( $fireNewActionsJob ) {
            $job = ( new SetSchedulesJob(
                'NewActions-' . $this->firstEspId . '-' . Carbon::now()->toDateTimeString() ,
                $this->newActions ,
                'activity' ,
                str_random(16)
            ) )->onQueue( self::QUEUE );

            $this->dispatch($job);        
        }

        return $count;
    }

    public function withinTwoDays($espId, $campaignId){
        return $this->repo->withinTwoDays($espId, $campaignId);
    }

}
