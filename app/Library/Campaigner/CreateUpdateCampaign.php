<?php
namespace App\Library\Campaigner;
class CreateUpdateCampaign
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var CampaignData $campaignData
     */
    protected $campaignData = null;

    /**
     * @param Authentication $authentication
     * @param CampaignData $campaignData
     */
    public function __construct($authentication, $campaignData)
    {
      $this->authentication = $authentication;
      $this->campaignData = $campaignData;
    }

    /**
     * @return Authentication
     */
    public function getAuthentication()
    {
      return $this->authentication;
    }

    /**
     * @param Authentication $authentication
     * @return CreateUpdateCampaign
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return CampaignData
     */
    public function getCampaignData()
    {
      return $this->campaignData;
    }

    /**
     * @param CampaignData $campaignData
     * @return CreateUpdateCampaign
     */
    public function setCampaignData($campaignData)
    {
      $this->campaignData = $campaignData;
      return $this;
    }

}
