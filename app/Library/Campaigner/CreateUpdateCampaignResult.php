<?php
namespace App\Library\Campaigner;
class CreateUpdateCampaignResult
{

    /**
     * @var int $CampaignId
     */
    protected $CampaignId = null;

    /**
     * @param int $CampaignId
     */
    public function __construct($CampaignId)
    {
      $this->CampaignId = $CampaignId;
    }

    /**
     * @return int
     */
    public function getCampaignId()
    {
      return $this->CampaignId;
    }

    /**
     * @param int $CampaignId
     * @return CreateUpdateCampaignResult
     */
    public function setCampaignId($CampaignId)
    {
      $this->CampaignId = $CampaignId;
      return $this;
    }

}
