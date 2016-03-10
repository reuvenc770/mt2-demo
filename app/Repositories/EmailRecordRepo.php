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
    protected $emailAction;
    protected $actionType;
    protected $emailDomain;
    protected $domainGroup;
    protected $emailClientInstance;


    public function __construct (
        Email $email ,
        EmailAction $emailAction ,
        ActionType $actionType ,
        EmailDomain $emailDomain ,
        DomainGroup $domainGroup ,
        EmailClientInstance $emailClientInstance
    ) {
        $this->email = $email;
        $this->emailAction = $emailAction;
        $this->actionType = $actionType;
        $this->emailDomain = $emailDomain;
        $this->domainGroup = $domainGroup;
        $this->emailClientInstance = $emailClientInstance;
    }

    public function getEmailId ( $email ) {
        #return $this->email->select( 'id' )->where( 'email_address' , $email )->get();

        return mt_rand( 1 , 100000 );
    }

    //These methods need to find the clientId to attribute the action to.
    public function recordOpen ( $emailId , $espId , $campaignId , $date ) {
        Log::info( "Recording Open: $emailId - $espId - $campaignId - $date" );
    }

    public function recordClick ( $emailId , $espId , $campaignId , $date ) {
        Log::info( "Recording Click: $emailId - $espId - $campaignId - $date" );
    }

    public function recordDeliverable ( $emailId , $espId , $campaignId , $date ) {
        Log::info( "Recording Deliverable: $emailId - $espId - $campaignId - $date" );
    }
}
