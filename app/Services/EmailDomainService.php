<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 9/29/16
 * Time: 3:30 PM
 */

namespace App\Services;


use App\Repositories\EmailDomainRepo;
use App\Services\ServiceTraits\PaginateList;
class EmailDomainService
{
    use PaginateList;
    protected $emailDomainRepo;

    public function __construct(EmailDomainRepo $emailDomainRepo)
    {
        $this->emailDomainRepo = $emailDomainRepo;
    }

    public function getModel(){
        return $this->emailDomainRepo->getModel();
    }


    public function getType(){
        return "EmailDomain";
    }

    public function getEmailDomainById($id){
        return $this->emailDomainRepo->getRow($id);
    }

    public function insertDomain($request){

        return $this->emailDomainRepo->insertRow($request);

    }

    public function updateDomain($id, $groupData){
        return $this->emailDomainRepo->updateRow($id, $groupData);
    }
}