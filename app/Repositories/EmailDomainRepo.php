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

    public function getDomainAndClassInfo($email) {
        $emailParts = explode('@', $email);
        if (isset($emailParts[1])) {
            return $this->emailDomainModel
                        ->selectRaw('email_domains.id as domain_id, domain_group_id, lower(dg.name) as domain_group_name')
                        ->join('domain_groups as dg', 'email_domains.domain_group_id', '=', 'dg.id')
                        ->where('domain_name', $emailParts[1])
                        ->first();

        }
    }

    public function createNewDomain($email) {
        $emailParts = explode('@', $email);
        if (isset($emailParts[1])) {
            // Precaution due to incompletely-parallelized feeds
            // Also default domain group id to 0
            $domain = EmailDomain::updateOrCreate([
                'domain_name' => strtolower($emailParts[1])
            ], [
                'domain_name' => strtolower($emailParts[1]),
                'domain_group_id' => 0
            ]);

            return $domain;
        }

        // Perhaps throw an exception here
    }

    public function domainIsSuppressed($domainId) {
        $result = $this->emailDomainModel->where('id', $domainId);
        if ($result) {
            return $result->is_suppressed === 1;
        }
        else {
            return false;
        }
    }


}