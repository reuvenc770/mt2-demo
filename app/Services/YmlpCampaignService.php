<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/21/16
 * Time: 2:39 PM
 */

namespace App\Services;


use App\Repositories\YmlpCampaignRepo;
use App\Services\ServiceTraits\PaginateListNoCache;

class YmlpCampaignService
{
    use PaginateListNoCache;

    protected $campaignRepo;

    public function __construct(YmlpCampaignRepo $campaign)
    {
        $this->campaignRepo = $campaign;
    }

    public function getAllCampaigns()
    {
        return $this->campaignRepo->getCampaigns();
    }

    public function getCampaignbyId($id){
        return $this->campaignRepo->getByid($id);
    }

    public function updateCampaign ( $id , $accountData ) {
       return $this->campaignRepo->updateCampaign( $id , $accountData );
    }

    public function insertCampaign($data){
       return $this->campaignRepo->insertCampaign($data);
    }

    public function getType () {
        return 'ymlpcampaign';
    }

    public function getModel () { return $this->campaignRepo->getModel(); }
}