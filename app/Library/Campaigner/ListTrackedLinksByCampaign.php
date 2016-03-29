<?php
namespace App\Library\Campaigner;
class ListTrackedLinksByCampaign
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var ArrayOfInt $campaignIds
     */
    protected $campaignIds = null;

    /**
     * @param Authentication $authentication
     * @param ArrayOfInt $campaignIds
     */
    public function __construct($authentication, $campaignIds)
    {
      $this->authentication = $authentication;
      $this->campaignIds = $campaignIds;
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
     * @return ListTrackedLinksByCampaign
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return ArrayOfInt
     */
    public function getCampaignIds()
    {
      return $this->campaignIds;
    }

    /**
     * @param ArrayOfInt $campaignIds
     * @return ListTrackedLinksByCampaign
     */
    public function setCampaignIds($campaignIds)
    {
      $this->campaignIds = $campaignIds;
      return $this;
    }

}
