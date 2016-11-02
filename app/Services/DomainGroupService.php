<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 9/28/16
 * Time: 3:14 PM
 */

namespace App\Services;


use App\Repositories\DomainGroupRepo;
use App\Repositories\EmailDomainRepo;
use App\Services\ServiceTraits\PaginateList;
use Log;
class DomainGroupService
{
    use PaginateList;
    protected $domainGroupRepo;

    public function __construct(DomainGroupRepo $domainGroupRepo)
    {
        $this->domainGroupRepo = $domainGroupRepo;
    }

    public function getModel(){
        return $this->domainGroupRepo->getModel();
    }


    public function getType(){
        return "DomainGroup";
    }

    public function getDomainGroupById($id){
        return $this->domainGroupRepo->getRow($id);
    }

    public function insertGroup($request){

            return $this->domainGroupRepo->insertRow($request);

    }

    public function updateGroup($id, $groupData){
        return $this->domainGroupRepo->updateRow($id, $groupData);
    }

    public function getAll(){
        return $this->domainGroupRepo->getAll();
    }

    public function getAllActiveNames () {
        return $this->domainGroupRepo->getAllActiveNames();
    }
}
