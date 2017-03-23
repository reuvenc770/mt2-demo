<?php

namespace App\Services;

use App\Events\NewActions;
use App\Models\ActionType;
use App\Models\EmailAction;
use App\Repositories\EmailRecordRepo;
use App\Repositories\RawDeliveredEmailRepo;
use Log;
use Carbon\Carbon;
use DB;

class EmailRecordService {
    protected $repo;
    private $deliveredRepo;
    protected $records = [];
    private $deliveredRecords = [];
    const MAX_RECORD_COUNT = 50000;

    public function __construct ( EmailRecordRepo $repo, RawDeliveredEmailRepo $deliveredRepo ) {
        $this->repo = $repo;
        $this->deliveredRepo = $deliveredRepo;
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
        $emailId = $this->getEmailId($email);

        if ( $this->repo->isValidActionType($recordType) || is_null($emailId)) {
            $this->records []= [
                    'recordType' => $recordType ,
                    'email' => $email ,
                    'deployId' => $deployId,
                    'espId' => $espId ,
                    'espInternalId' => $espInternalId ,
                    'date' => $date
                ];

            if (self::MAX_RECORD_COUNT <= sizeof($this->records)) {
                $this->massRecordActions();
            }   
        }
        elseif ($this->deliveredRepo->isValidActionType($recordType)) {

            $this->deliveredRecords []= [
                    'email_id' => $emailId,
                    'deploy_id' => $deployId,
                    'esp_account_id' => $espId ,
                    'esp_internal_id' => $espInternalId ,
                    'datetime' => $date
                ];

            if (self::MAX_RECORD_COUNT <= sizeof($this->deliveredRecords)) {
                $this->massRecordDelivered();
            }
        }
        else {
            Log::error( "Record Type '{$recordType}' is not valid." );
            return false;
        }
    }

    private function massRecordActions() {
        try {
            $this->repo->massRecordDeliverables($this->records);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        finally {
            $this->records = [];
        }
    }

    private function massRecordDelivered() {
        try {
            $this->deliveredRepo->massInsert($this->deliveredRecords);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        finally {
            $this->deliveredRecords = [];
        }
    }

    public function massRecordDeliverables () {
        $count = count($this->records);
        $deliveredCount = count($this->deliveredRecords);

        try {
            $this->repo->massRecordDeliverables($this->records);
            $this->deliveredRepo->massInsert($this->deliveredRecords);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $this->records = []; // clear out to free up space
            $this->deliveredRecords = [];
        }
        return $count + $deliveredCount;
    }

    public function withinTwoDays($espId, $campaignId){
        return $this->repo->withinTwoDays($espId, $campaignId);
    }

}
