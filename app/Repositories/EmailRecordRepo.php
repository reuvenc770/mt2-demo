<?php

namespace App\Repositories;

use App\Models\Email;
use App\Models\EmailAction;
use App\Models\ActionType;
use App\Models\EmailClientInstance;
use App\Models\OrphanEmail;

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

    public function __construct ( Email $email ) {
        $this->email = $email;
    }

    public function recordDeliverable ( $recordType , $emailAddress , $espId , $campaignId , $date ) {
        $this->setLocalData( [
            'emailAddress' => $emailAddress ,
            'recordType' => $recordType ,
            'espId' => $espId ,
            'campaignId' => $campaignId ,
            'date' => $date
        ] );

        if ( $this->isValidRecord() ) {
            DB::connection( 'reporting_data' )->statement("
                INSERT INTO email_actions
                    ( email_id , client_id , esp_account_id , campaign_id , action_id , datetime , created_at , updated_at )    
                VALUES
                    ( ? , ? , ? , ? , ? , ? , NOW() , NOW() )
                ON DUPLICATE KEY UPDATE
                    email_id = email_id ,
                    client_id = client_id ,
                    esp_account_id = esp_account_id ,
                    campaign_id = campaign_id ,
                    action_id = action_id ,
                    datetime = datetime ,
                    created_at = created_at ,
                    updated_at = NOW()" ,
                [
                    $this->getEmailId() ,
                    $this->getClientId() ,
                    $espId ,
                    $campaignId ,
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
        $this->campaignId = $recordData[ 'campaignId' ];
        $this->date = $recordData[ 'date' ];
    }

    protected function isValidRecord () {
        $orphan = new OrphanEmail();
        $errorFound = false;

        if ( !$this->emailExists() ) {
            $orphan->missing_email_record = 1;

            //Log::error( "Email '{$this->emailAddress}' does not exist." );

            $errorFound = true;
        } elseif ( $this->emailExists() && !$this->hasClient() ) {
            $orphan->missing_email_client_instance = 1;

            Log::error( "Client ID for email '{$this->emailAddress}' does not exist." );

            $errorFound = true;
        }

        if ( $errorFound ) {
            $orphan->email_address = $this->emailAddress;
            $orphan->esp_account_id = $this->espId;
            $orphan->campaign_id = $this->campaignId;
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

        $emailClientInstances = Email::find( $this->getEmailId() )->emailClientInstances();

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
}
