<?php
namespace App\Library\Campaigner;
class StopCampaign
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var int $campaignId
     */
    protected $campaignId = null;

    /**
     * @param Authentication $authentication
     * @param int $campaignId
     */
    public function __construct($authentication, $campaignId)
    {
      $this->authentication = $authentication;
      $this->campaignId = $campaignId;
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
     * @return StopCampaign
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return int
     */
    public function getCampaignId()
    {
      return $this->campaignId;
    }

    /**
     * @param int $campaignId
     * @return StopCampaign
     */
    public function setCampaignId($campaignId)
    {
      $this->campaignId = $campaignId;
      return $this;
    }

}
