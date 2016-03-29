<?php
namespace App\Library\Campaigner;
class CreateUpdateCampaignResponse
{

    /**
     * @var CreateUpdateCampaignResult $CreateUpdateCampaignResult
     */
    protected $CreateUpdateCampaignResult = null;

    /**
     * @param CreateUpdateCampaignResult $CreateUpdateCampaignResult
     */
    public function __construct($CreateUpdateCampaignResult)
    {
      $this->CreateUpdateCampaignResult = $CreateUpdateCampaignResult;
    }

    /**
     * @return CreateUpdateCampaignResult
     */
    public function getCreateUpdateCampaignResult()
    {
      return $this->CreateUpdateCampaignResult;
    }

    /**
     * @param CreateUpdateCampaignResult $CreateUpdateCampaignResult
     * @return CreateUpdateCampaignResponse
     */
    public function setCreateUpdateCampaignResult($CreateUpdateCampaignResult)
    {
      $this->CreateUpdateCampaignResult = $CreateUpdateCampaignResult;
      return $this;
    }

}
