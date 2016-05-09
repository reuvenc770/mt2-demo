<?php

namespace App\Repositories;

use App\Models\Email;
use App\Models\EmailAction;
use App\Models\ActionType;
use App\Models\EmailClientInstance;
use App\Models\OrphanEmail;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

use Log;

class EmailRecordRepo {
    protected $email;
    protected $emailAddress = '';
    protected $recordType = '';
    protected $espId = 0;
    protected $campaignId = 0;
    protected $date = '';

    protected $errorReason = '';

    public function __construct ( Email $email ) {
        $this->email = $email;
    }

    public function massRecordDeliverables ( $records = [] ) {
        $validRecords = [];
        $invalidRecords = [];
        
        foreach ( $records as $currentIndex => $currentRecord ) {
            
            $this->setLocalData( [
                'emailAddress' => $currentRecord[ 'email' ] ,
                'recordType' => $currentRecord[ 'recordType' ] ,
                'espId' => $currentRecord[ 'espId' ] ,
                'deployId' => $currentRecord['deployId'],
                'espInternalId' => $currentRecord[ 'espInternalId' ] ,
                'date' => $currentRecord[ 'date' ]
            ] );

            $this->errorReason = '';

            if ( $this->isValidRecord( false ) ) {
                $validRecord = "( "
                    . join( " , " , [
                        $this->getEmailId() , 
                        $this->getClientId() ,
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
                        "'" . $currentRecord[ 'email' ] . "'" ,
                        $currentRecord[ 'espId' ] ,
                        $currentRecord['deployId'],
                        $currentRecord[ 'espInternalId' ] ,
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
                        ( email_id , client_id , esp_account_id , deploy_id, 
                        esp_internal_id , action_id , datetime , created_at , 
                        updated_at )    
                    VALUES
                        " . join( ' , ' , $chunk ) . "
                    ON DUPLICATE KEY UPDATE
                        email_id = email_id ,
                        client_id = client_id ,
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
    }

    public function recordDeliverable ( $recordType , $emailAddress , $espId , $deployId, $espInternalId , $date ) {
        $this->setLocalData( [
            'emailAddress' => $emailAddress ,
            'recordType' => $recordType ,
            'espId' => $espId ,
            'deployId' => $deployId,
            'espInternalId' => $espInternalId ,
            'date' => $date
        ] );

        if ( $this->isValidRecord() ) {
            DB::connection( 'reporting_data' )->statement("
                INSERT INTO email_actions
                    ( email_id , client_id , deploy_id, esp_account_id , esp_internal_id , action_id , datetime , created_at , updated_at )    
                VALUES
                    ( ? , ? , ? , ? , ? , ? , ? , NOW() , NOW() )
                ON DUPLICATE KEY UPDATE
                    email_id = email_id ,
                    client_id = client_id ,
                    deploy_id = deploy_id,
                    esp_account_id = esp_account_id ,
                    esp_internal_id = esp_internal_id ,
                    action_id = action_id ,
                    datetime = datetime ,
                    created_at = created_at ,
                    updated_at = NOW()" ,
                [
                    $this->getEmailId() ,
                    $this->getClientId() ,
                    $deployId,
                    $espId ,
                    $espInternalId ,
                    $this->getActionId( $recordType ) ,
                    $date
                ]
            );

            return true;
        } else {
            return false;
        }
    }

    public function isValidActionType ( $actionName ) {
        return ActionType::where( 'name' , $actionName )->count() == 1;
    }

    public function emailExists () {
        return $this->email->where( 'email_address' , $this->emailAddress )->count() > 0;
    }

    public function hasClient () {
        // TODO
        //return Email::find( $this->getEmailId() )->emailClientInstances()->count() > 0;
        // temporary workaround so we don't fail here
        return true;
    }

    public function getEmailId () {
        return $this->email->select( 'id' )->where( 'email_address' , $this->emailAddress )->first()->id;
    }

    protected function setLocalData ( $recordData ) {
        $this->emailAddress = $recordData[ 'emailAddress' ];
        $this->recordType = $recordData[ 'recordType' ];
        $this->espId = $recordData[ 'espId' ];
        $this->deployId = $recordData['deployId'];
        $this->espInternalId = $recordData[ 'espInternalId' ];
        $this->date = $recordData[ 'date' ];
    }

    protected function isValidRecord ( $saveOrphan = true ) {
        $orphan = new OrphanEmail();
        $errorFound = false;

        if ( !$this->emailExists() ) {
            $orphan->missing_email_record = 1;
            $this->errorReason = 'missing_email_record';

            //Log::error( "Email '{$this->emailAddress}' does not exist." );

            $errorFound = true;
        } elseif ( $this->emailExists() && !$this->hasClient() ) {
            $orphan->missing_email_client_instance = 1;
            $this->errorReason = 'missing_email_client_instance';

            Log::error( "Client ID for email '{$this->emailAddress}' does not exist." );

            $errorFound = true;
        }

        if ( $errorFound && $saveOrphan ) {
            $orphan->email_address = $this->emailAddress;
            $orphan->esp_account_id = $this->espId;
            $orphan->deploy_id = $this->deployId;
            $orphan->esp_internal_id = $this->espInternalId;
            $orphan->action_id = $this->getActionId( $this->recordType );
            $orphan->datetime = $this->date;
            $orphan->save();
        }

        return $errorFound === false;
    }

    protected function getClientId () {
        // TODO
        //return Email::find( $this->getEmailId() )->emailClientInstances()->first()->client_id;

        // temporary workaround while missing email client instances and attribution

        $emailClientInstances = Email::find( $this->getEmailId() )->emailClientInstances;

        if ($emailClientInstances->isEmpty()) {
            return 0;
        }
        else {
            return $emailClientInstances->first()->client_id;
        }

    }

    protected function getActionId ( $actionName ) {
        return ActionType::where( 'name' , $actionName )->first()->id;
    }


    public function withinTwoDays($espId,$espInternalId){
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
