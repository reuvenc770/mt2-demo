<?php
namespace App\Library\Campaigner;
class ListTrackedLinksByCampaignResponse
{

    /**
     * @var ArrayOfTrackedLinkDescription $ListTrackedLinksByCampaignResult
     */
    protected $ListTrackedLinksByCampaignResult = null;

    /**
     * @param ArrayOfTrackedLinkDescription $ListTrackedLinksByCampaignResult
     */
    public function __construct($ListTrackedLinksByCampaignResult)
    {
      $this->ListTrackedLinksByCampaignResult = $ListTrackedLinksByCampaignResult;
    }

    /**
     * @return ArrayOfTrackedLinkDescription
     */
    public function getListTrackedLinksByCampaignResult()
    {
      return $this->ListTrackedLinksByCampaignResult;
    }

    /**
     * @param ArrayOfTrackedLinkDescription $ListTrackedLinksByCampaignResult
     * @return ListTrackedLinksByCampaignResponse
     */
    public function setListTrackedLinksByCampaignResult($ListTrackedLinksByCampaignResult)
    {
      $this->ListTrackedLinksByCampaignResult = $ListTrackedLinksByCampaignResult;
      return $this;
    }

}
