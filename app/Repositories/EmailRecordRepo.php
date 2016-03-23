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
            $emailAction = new EmailAction();
            $emailAction->email_id = $this->getEmailId();
            $emailAction->client_id = $this->getClientId();
            $emailAction->esp_account_id = $espId;
            $emailAction->campaign_id = $campaignId;
            $emailAction->action_id = $this->getActionId( $recordType );
            $emailAction->datetime = $date;
            $emailAction->save();
        }
    }

    public function isValidActionType ( $actionName ) {
        return ActionType::where( 'name' , $actionName )->count() == 1;
    }

    public function emailExists () {
        return $this->email->where( 'email_address' , $this->emailAddress )->count() > 0;
    }

    public function hasClient () {
        return Email::find( $this->getEmailId() )->emailClientInstances()->count() > 0;
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

            Log::error( "Email '{$this->emailAddress}' does not exist." );

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

    protected function getEmailId () {
        return $this->email->select( 'id' )->where( 'email_address' , $this->emailAddress )->first()->id;
    }

    protected function getClientId () {
        return Email::find( $this->getEmailId() )->emailClientInstances()->first()->client_id;
    }

    protected function getActionId ( $actionName ) {
        return ActionType::where( 'name' , $actionName )->first()->id;
    }
}
