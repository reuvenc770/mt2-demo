<?php

namespace App\Services;

use App\Repositories\EmailRecordRepo;
use Log;
use Carbon\Carbon;
use DB;

class EmailRecordService {
    protected $repo;
    protected $records = [];

    public function __construct ( EmailRecordRepo $repo ) {
        $this->repo = $repo;
    }

    public function getEmailId ( $email ) {
        return $this->repo->getEmailId( $email );
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
        } else {
            Log::error( "Record Type '{$recordType}' is not valid." );
            return false;
        }
    }

    public function massRecordDeliverables () {
        try {
            $this->repo->massRecordDeliverables($this->records);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function checkTwoDays($espId,$espInternalId){
        $delivevered = false;
        $date = Carbon::today()->subDay(2)->toDateTimeString();
        $actionCount = DB::connection( 'reporting_data' )->table('standard_reports')
            ->where('esp_account_id', $espId)
            ->where('esp_internal_id',$espInternalId)
            ->where('datetime','>=', $date)->count();
        if ($actionCount == 1) {
            $delivevered = true;
        }
        return $delivevered;
    }
}
