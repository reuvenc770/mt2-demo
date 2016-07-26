<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/22/16
 * Time: 1:44 PM
 */

namespace App\Services;


use App\Repositories\DomainRepo;
use App\Services\ServiceTraits\PaginateList;

class DomainService
{
    protected $domainRepo;
    use PaginateList;

    public function __construct(DomainRepo $domainRepo)
    {
        $this->domainRepo = $domainRepo;
    }

    public function getModel()
    {
        return $this->domainRepo->getModel();
    }

    public function getDomainsByTypeAndEsp($type, $espAccountId)
    {
        return $this->domainRepo->getDomainsByTypeAndEsp($type, $espAccountId);
    }

    public function insertDomains($insertArray){
        foreach ($insertArray as $item){
            $this->domainRepo->insertRow($item);
        }
        return true;
    }
}