<?php
namespace App\Library\Campaigner;
class GetTrackedLinkSummaryReport
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var int $campaignRunId
     */
    protected $campaignRunId = null;

    /**
     * @param Authentication $authentication
     * @param int $campaignRunId
     */
    public function __construct($authentication, $campaignRunId)
    {
      $this->authentication = $authentication;
      $this->campaignRunId = $campaignRunId;
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
     * @return GetTrackedLinkSummaryReport
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return int
     */
    public function getCampaignRunId()
    {
      return $this->campaignRunId;
    }

    /**
     * @param int $campaignRunId
     * @return GetTrackedLinkSummaryReport
     */
    public function setCampaignRunId($campaignRunId)
    {
      $this->campaignRunId = $campaignRunId;
      return $this;
    }

}
