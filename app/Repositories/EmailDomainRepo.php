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


    public function getModel(){
        return $this->emailDomainModel
            ->join("domain_groups","email_domains.domain_group_id", "=", "domain_groups.id")
            ->select("email_domains.id","email_domains.domain_name","domain_groups.name as domain_group");
    }

    public function getRow($id){
        return $this->emailDomainModel->find($id);
    }

    public function updateRow($id, $groupData){
        return $this->emailDomainModel->find( $id )->update( $groupData);
    }

    public function insertRow($data){
        return $this->emailDomainModel->create($data);
    }


}