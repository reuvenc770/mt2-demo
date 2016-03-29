<?php
namespace App\Library\Campaigner;
class ListCampaignsResponse
{

    /**
     * @var ArrayOfCampaignDescription $ListCampaignsResult
     */
    protected $ListCampaignsResult = null;

    /**
     * @param ArrayOfCampaignDescription $ListCampaignsResult
     */
    public function __construct($ListCampaignsResult)
    {
      $this->ListCampaignsResult = $ListCampaignsResult;
    }

    /**
     * @return ArrayOfCampaignDescription
     */
    public function getListCampaignsResult()
    {
      return $this->ListCampaignsResult;
    }

    /**
     * @param ArrayOfCampaignDescription $ListCampaignsResult
     * @return ListCampaignsResponse
     */
    public function setListCampaignsResult($ListCampaignsResult)
    {
      $this->ListCampaignsResult = $ListCampaignsResult;
      return $this;
    }

}
