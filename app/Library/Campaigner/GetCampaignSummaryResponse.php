<?php
namespace App\Library\Campaigner;
class GetCampaignSummaryResponse
{

    /**
     * @var CampaignSummary $GetCampaignSummaryResult
     */
    protected $GetCampaignSummaryResult = null;

    /**
     * @param CampaignSummary $GetCampaignSummaryResult
     */
    public function __construct($GetCampaignSummaryResult)
    {
      $this->GetCampaignSummaryResult = $GetCampaignSummaryResult;
    }

    /**
     * @return CampaignSummary
     */
    public function getGetCampaignSummaryResult()
    {
      return $this->GetCampaignSummaryResult;
    }

    /**
     * @param CampaignSummary $GetCampaignSummaryResult
     * @return GetCampaignSummaryResponse
     */
    public function setGetCampaignSummaryResult($GetCampaignSummaryResult)
    {
      $this->GetCampaignSummaryResult = $GetCampaignSummaryResult;
      return $this;
    }

}
