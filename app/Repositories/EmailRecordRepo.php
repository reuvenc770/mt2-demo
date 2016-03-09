<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

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
}
