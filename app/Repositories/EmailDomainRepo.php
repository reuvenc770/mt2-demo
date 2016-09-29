<?php

namespace App\Repositories;

use App\Models\EmailDomain;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class EmailDomainRepo {

    private $emailDomainModel;

    public function __construct(EmailDomain $emailDomainModel) {
        $this->emailDomainModel = $emailDomainModel;
    }

    public function getIdForName($email) {
        $emailParts = explode('@', $email);
        if (sizeof($emailParts) > 1) {
            $lowerName = strtolower($emailParts[1]);
            $result = $this->emailDomainModel->select('id')->where('domain_name', $lowerName)->get();

            if (sizeof($result) > 0 && isset($result[0]['id'])) {
                return $result[0]['id'];
            }
        }

        return 0;
    }

    public function getAll(){
        return $this->emailDomainModel->all();
    }


}