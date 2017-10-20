<?php

namespace App\Repositories;

use App\Models\Email;
use App\Models\EmailAction;
use App\Models\ActionType;
use App\Models\EmailFeedInstance;
use App\Models\OrphanEmail;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Services\AbstractReportService;
use Log;
use App\Events\NewActions;

class EmailRecordRepo {
    protected $email;
    protected $emailId = null;
    protected $emailAddress = '';
    protected $recordType = '';
    protected $espId = 0;
    protected $campaignId = 0;
    protected $date = '';

    protected $errorReason = '';

    public function __construct ( Email $email ) {
        $this->email = $email;
    }

    public function massRecordDeliverables ($records) {
        $validRecords = [];
        $invalidRecords = [];
        $pdo = DB::connection()->getPdo();

        foreach ( $records as $currentIndex => $currentRecord ) {
            
            $this->setLocalData( [
                'emailId' => $this->getEmailId($currentRecord['email']),
                'emailAddress' => $currentRecord[ 'email' ] ,
                'recordType' => $currentRecord[ 'recordType' ] ,
                'espId' => $currentRecord[ 'espId' ] ,
                'deployId' => $currentRecord['deployId'],
                'espInternalId' => $currentRecord[ 'espInternalId' ] ,
                'date' => $currentRecord[ 'date' ]
            ] );

            $this->errorReason = '';

            if ( $this->isValidRecord() ) {
                $validRecord = "( "
                    . join( " , " , [
                        $this->emailId ,
                        $currentRecord[ 'espId' ] ,
                        $currentRecord['deployId'],
                        $currentRecord[ 'espInternalId' ] ,
                        $this->getActionId( $currentRecord[ 'recordType' ] ) ,
                        ( empty( $currentRecord[ 'date' ] ) ? "''" : "'" . $currentRecord[ 'date' ] . "'" ) ,
                        'NOW()' ,
                        'NOW()'
                    ] )
                    . " )";

                $validRecords []= $validRecord;
            } else {
                $invalidRecord = "( " 
                    .join( " , " , [
                        $pdo->quote($currentRecord[ 'email' ]) ,
                        $currentRecord[ 'espId' ] ,
                        is_numeric($currentRecord['deployId']) ? $currentRecord['deployId'] : 0,
                        is_numeric($currentRecord['espInternalId']) ? $currentRecord[ 'espInternalId' ] : 0,
                        $this->getActionId( $currentRecord[ 'recordType' ] ) ,
                        ( empty( $currentRecord[ 'date' ] ) ? "''" : "'" . $currentRecord[ 'date' ] . "'" ) ,
                        ( $this->errorReason == 'missing_email_record' ? 1 : 0 ) ,
                        ( $this->errorReason == 'missing_email_client_instance' ? 1 : 0 ) ,
                        'NOW()' ,
                        'NOW()'
                    ] )
                    . " )";

                $invalidRecords []= $invalidRecord;
            }
        }

        if ( !empty( $validRecords ) ) {
            $chunkedRecords = array_chunk( $validRecords , 10000 );

            foreach ( $chunkedRecords as $chunkIndex => $chunk ) {
                DB::connection( 'reporting_data' )->statement("
                    INSERT INTO email_actions
                        ( email_id , esp_account_id , deploy_id, 
                        esp_internal_id , action_id , datetime , created_at , 
                        updated_at )    
                    VALUES
                        " . join( ' , ' , $chunk ) . "
                    ON DUPLICATE KEY UPDATE
                        email_id = email_id ,
                        esp_account_id = esp_account_id ,
                        deploy_id = deploy_id,
                        esp_internal_id = esp_internal_id ,
                        action_id = action_id ,
                        datetime = datetime ,
                        created_at = created_at ,
                        updated_at = NOW()"
                    );
            }
        }

        if ( !empty( $invalidRecords ) ) {
            $chunkedRecords = array_chunk( $invalidRecords , 10000 );

            foreach ( $chunkedRecords as $chunkIndex => $chunk ) {
                DB::statement( "
                    INSERT INTO     
                        orphan_emails ( email_address , esp_account_id , 
                        deploy_id, esp_internal_id , action_id , 
                        datetime , missing_email_record , 
                        missing_email_client_instance , created_at , updated_at )
                    VALUES
                    " . join( ' , ' , $chunk )
                );
            }
        }

        $validRecords = null;
        $invalidRecords = null;
    }


    public function isValidActionType ( $actionName ) {
        return in_array($actionName, ['opener', 'clicker']);
    }

    public function emailExists () {
        return $this->email->where( 'email_address' , $this->emailAddress )->count() > 0;
    }

    public function hasDeployId() {
        return $this->deployId !== 0;
    }

    public function getEmailId ( $emailAddress = null ) {
        $obj =  $this->email->select( 'id' )->where('email_address', ($emailAddress ?: $this->emailAddress))->first();

        if ($obj) {
            return $obj->id;
        }
        else {
            return null;
        }
    }

    public function getEmailAddress($eid){
        try {
            return $this->email->find($eid)->email_address;
        } catch (\Exception $e){
            return false;
        }
    }

    protected function setLocalData ( $recordData ) {
        $this->emailId = $recordData['emailId'];
        $this->emailAddress = $recordData[ 'emailAddress' ];
        $this->recordType = $recordData[ 'recordType' ];
        $this->espId = $recordData[ 'espId' ];
        $this->deployId = (int)$recordData['deployId'];
        $this->espInternalId = $recordData[ 'espInternalId' ];
        $this->date = $recordData[ 'date' ];
    }

    protected function isValidRecord () {
        $errorFound = false;

        if (null === $this->emailId) {
            $this->errorReason = 'missing_email_record';
            $errorFound = true;
        } elseif (!$this->hasDeployId()) {
            $this->errorReason = 'missing_deploy_id';
            $errorFound = true;
        }

        return $errorFound === false;
    }


    protected function getActionId ( $actionName ) {
        return ActionType::where( 'name' , $actionName )->first()->id;
    }


    public function withinTwoDays($espId,$espInternalId){
        $delivered = false;
        $date = Carbon::today()->subDay(2)->toDateTimeString();
        $actionCount = DB::connection( 'reporting_data' )->table('standard_reports')
            ->where('esp_account_id', $espId)
            ->where('esp_internal_id',$espInternalId)
            ->where('datetime','>=', $date)->count();
        if ($actionCount == 1) {
            $delivered = true;
        }
        return $delivered;
    }
    
}
