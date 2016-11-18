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
class DomainGroupRepo
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
        return $this->domainGroup->find($id);
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
}
