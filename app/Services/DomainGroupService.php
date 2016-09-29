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

class DomainGroupService
{
    use PaginateList;
    protected $domainGroupRepo;
    protected $emailDomainRepo;

    public function __construct(DomainGroupRepo $domainGroupRepo, EmailDomainRepo $emailDomainRepo)
    {
        $this->domainGroupRepo = $domainGroupRepo;
        $this->emailDomainRepo = $emailDomainRepo;
    }

    public function getModel(){
        return $this->domainGroupRepo->getModel();
    }

    public function getDomains(){
        return $this->emailDomainRepo->getAll();
    }

    public function getType(){
        return "DomainGroup";
    }

}