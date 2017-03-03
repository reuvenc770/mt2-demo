<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 9/28/16
 * Time: 3:12 PM
 */

namespace App\Repositories;

use App\Models\DomainGroup;
use DB;
use App\Repositories\RepoInterfaces\IAwsRepo;

class DomainGroupRepo implements IAwsRepo
{
    protected $domainGroup;

    public function __construct(DomainGroup $domainGroup)
    {
        $this->domainGroup = $domainGroup;
    }

    public function getModel(){
        return $this->domainGroup
            ->leftJoin("email_domains", 'domain_groups.id', '=', 'email_domains.domain_group_id')
            ->select(DB::raw("domain_groups.id, domain_groups.name, count(email_domains.id) as domainCount, country, domain_groups.status"))
            ->groupBy("domain_groups.name")
            ->orderBy("domain_groups.status");
    }

    public function getRow($id){
        return $this->domainGroup->with( 'domains' )->find($id);
    }

    public function updateRow($id, $groupData){
        return $this->domainGroup->find( $id )->update( $groupData);
    }

    public function insertRow($data){
        return $this->domainGroup->create($data);
    }

    public function toggleRow($id, $direction){
        return $this->domainGroup->find($id)->update(["status" => $direction]);
    }

    public function getAll(){
        return $this->domainGroup->orderBy('name')->get();
    }

    public function getAllActive(){
        return $this->domainGroup->where('status', 'Active')->orderBy('name')->get();
    }

    public function getAllActiveNames () {
        return $this->domainGroup->where( 'status' , 'Active' )->pluck( 'name' )->toArray();
    }

    public function extractForS3Upload($stopPoint) {
        return $this->domainGroup->whereRaw("id > $stopPoint");
    }

    public function extractAllForS3() {
        return $this->domainGroup;
    }

    public function specialExtract($data) {}

    public function mapForS3Upload($row) {
        $pdo = DB::connection('redshift')->getPdo();
        return $pdo->quote($row->id) . ','
            . $pdo->quote($row->name) . ','
            . $pdo->quote($row->priority) . ','
            . $pdo->quote($row->status) . ','
            . $pdo->quote($row->country);
    }

    public function getConnection() {
        return $this->domainGroup->getConnectionName();
    }

    public function getCount() {
        return $this->domainGroup->count();
    }
}
