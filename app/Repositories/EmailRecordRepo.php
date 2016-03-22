<?php

namespace App\Repositories;

use App\Models\Email;
use App\Models\EmailAction;
use App\Models\ActionType;
use App\Models\EmailDomain;
use App\Models\DomainGroup;
use App\Models\EmailClientInstance;

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

        if ( !$this->emailExists() ) {
            Log::error( "Email '{$emailAddress}' does not exist." );
            return;
        }

        if ( !$this->hasClient() ) {
            Log::error( "Client ID for email '{$emailAddress}' does not exist." );
            return;
        }

        $emailAction = new EmailAction();
        $emailAction->email_id = $this->getEmailId();
        $emailAction->client_id = $this->getClientId();
        $emailAction->esp_account_id = $espId;
        $emailAction->campaign_id = $campaignId;
        $emailAction->action_id = $recordType;
        $emailAction->datetime = $date;
        $emailAction->save();
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

    protected function getEmailId () {
        return $this->email->select( 'id' )->where( 'email_address' , $this->emailAddress )->first()->id;
    }

    protected function getClientId () {
        return Email::find( $this->getEmailId() )->emailClientInstances()->first()->client_id;
    }
}
