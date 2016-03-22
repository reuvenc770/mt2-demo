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

    public function __construct ( Email $email ) {
        $this->email = $email;
    }

    public function recordDeliverable ( $recordType , $emailAddress , $espId , $campaignId , $date ) {
        $this->setEmailAddress( $emailAddress );

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

    protected function setEmailAddress ( $emailAddress ) {
        $this->emailAddress = $emailAddress;
    }

    protected function isValidRecord () {
        $errorFound = false;

        if ( !$this->emailExists() ) {
            if ( OrphanEmail::where( 'email_address' , $this->emailAddress )->count() > 0 ) {
                OrphanEmail::where( 'email_address' , $this->emailAddress )
                    ->update( [ 'missing_email_record' => 1 ] );
            } else {
                $orphan = new OrphanEmail();
                $orphan->email_address = $this->emailAddress;
                $orphan->missing_email_record = 1;
                $orphan->save();
            }

            Log::error( "Email '{$this->emailAddress}' does not exist." );

            $errorFound = true;
        }

        if ( $this->emailExists() && !$this->hasClient() ) {
            if ( OrphanEmail::where( 'email_address' , $this->emailAddress )->count() > 0 ) {
                OrphanEmail::where( 'email_address' , $this->emailAddress )
                    ->update( [ 'missing_email_client_instance' => 1 ] );
            } else {
                $orphan = new OrphanEmail();
                $orphan->email_address = $this->emailAddress;
                $orphan->missing_email_client_instance = 1;
                $orphan->save();
            }

            Log::error( "Client ID for email '{$this->emailAddress}' does not exist." );

            $errorFound = true;
        }

        Log::error( 'Error Found: ' . json_encode( [ $errorFound ] ) );

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
