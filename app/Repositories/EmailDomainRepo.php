<?php

namespace App\Repositories;

use App\Models\EmailDomain;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Repositories\RepoInterfaces\IAwsRepo;

/**
 *
 */
class EmailDomainRepo implements IAwsRepo {

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
            else {
                $id = $this->emailDomainModel->insertGetId([
                    'domain_group_id' => 0,
                    'domain_name' => $lowerName,
                    'is_suppressed' => 0
                ]);

                return $id;
            }
        }

        return 0;
    }

    public function getAll(){
        return $this->emailDomainModel->all();
    }


    public function getModel($searchData){
        $query = $this->emailDomainModel
            ->join("domain_groups","email_domains.domain_group_id", "=", "domain_groups.id")
            ->select("email_domains.id","email_domains.domain_name","domain_groups.name as domain_group");

        if ('' !== $searchData ) {
            $query = $this->mapQuery( $searchData , $query );
        }
        return $query;
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
            $lowerDomain = strtolower($emailParts[1]); // not explicitly necessary - mysql string comparisons are case insensitive
            return $this->emailDomainModel
                        ->selectRaw('email_domains.id as domain_id, IFNULL(domain_group_id, 0) as domain_group_id, lower(dg.name) as domain_group_name')
                        ->leftJoin('domain_groups as dg', 'email_domains.domain_group_id', '=', 'dg.id')
                        ->where('domain_name', $lowerDomain)
                        ->first();

        }
        else {
            return null; // this should throw an exception
        }
    }

    public function createNewDomain($email) {
        $emailParts = explode('@', $email);
        if (isset($emailParts[1])) {
            // Precaution due to incompletely-parallelized feeds
            // Also default domain group id to 0
            $domain = $this->emailDomainModel->updateOrCreate([
                'domain_name' => strtolower($emailParts[1])
            ], [
                'id' => null,
                'domain_name' => strtolower($emailParts[1]),
                'domain_group_id' => 0,
                'is_suppressed' => 0
            ]);

            return $domain;
        }

        // Perhaps throw an exception here
    }

    public function domainIsSuppressed($domainId) {
        $result = $this->emailDomainModel->where('id', $domainId)->first();
        if ($result) {
            return $result->is_suppressed === 1;
        }
        else {
            return false;
        }
    }

    public function mapQuery( $searchData , $query ) {
        $searchData = json_decode($searchData, true);

        if ( isset($searchData['domainGroupId']) ) {
            $query = $query->where( 'email_domains.domain_group_id' , (int)$searchData['domainGroupId'] );
        }

        return $query;
    }

    public function extractForS3Upload($startPoint) {
        return $this->emailDomainModel->whereRaw("id > $startPoint");
    }

    public function extractAllForS3() {
        return $this->emailDomainModel;
    }

    public function mapForS3Upload($row) {
        $pdo = DB::connection('redshift')->getPdo();
        return $pdo->quote($row->id) . ','
            . $pdo->quote($row->domain_group_id) . ','
            . $pdo->quote($row->domain_name) . ','
            . $pdo->quote($row->is_suppressed);
    }

    public function specialExtract($data) {}

    public function getConnection() {
        return $this->emailDomainModel->getConnectionName();
    }

    public function getCount() {
        return $this->emailDomainModel->count();
    }

}