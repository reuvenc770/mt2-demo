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

    public function getActiveDomainsByTypeAndEsp($type, $espAccountId)
    {
        return $this->domainRepo->getActiveDomainsByTypeAndEsp($type, $espAccountId);
    }

    public function insertDomains($insertArray){
        foreach ($insertArray as $item){
            $this->domainRepo->insertRow($item);
        }
        return true;
    }

    public function toggleRow($id, $direction){
        return $this->domainRepo->toggleRow($id,$direction);
    }

    public function inactivateDomain($id){
        return $this->domainRepo->inactivateDomain($id);
    }

    public function getType(){
        return "Domain";
    }

    public function getExpiringDomainsByDate($date){
        return $this->domainRepo->getDomainsByExpiration($date);
    }

    public function getDomain($id){
        return $this->domainRepo->getRow($id);
    }

    public function updateDomain($domain){
        $this->domainRepo->updateRow($domain);
    }

    public function searchDomains($searchData){
        return $this->domainRepo->getDomainsBySearch($searchData);
    }

    public function domainExistsAsDomainType( $domainName , $domainType ){
        $domainList = $this->domainRepo->getDomainsByDomainType( $domainType );

        $domainExists = in_array($domainName, $domainList);

        return $domainExists;
    }

    public function getDomainIdByTypeAndName($type,$name){
        $domainId = $this->domainRepo->getDomainIdByTypeAndName($type, $name);
        return ( is_null($domainId) ? 0 : $domainId->id );
    }

}