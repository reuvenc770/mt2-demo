<?php

namespace App\Repositories;

use App\Models\EmailClientInstance;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class EmailClientInstanceRepo {

    private $emailClientModel;

    public function __construct(EmailClientInstance $emailClientModel) {
        $this->emailClientModel = $emailClientModel;
    }

    public function getEmailId($emailAddress) {
        #return $this->emailModel->select( 'id' )->where( 'email_address' , $email )->get();
        return mt_rand(1, 100000);
    }

    public function insert($emailClientData) {

        
        $this->emailClientModel->insert($emailClientData);
    }

}