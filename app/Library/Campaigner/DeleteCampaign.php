<?php
namespace App\Library\Campaigner;
class DeleteCampaign
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
     * @var boolean $deleteReports
     */
    protected $deleteReports = null;

    /**
     * @param Authentication $authentication
     * @param int $campaignId
     * @param boolean $deleteReports
     */
    public function __construct($authentication, $campaignId, $deleteReports)
    {
      $this->authentication = $authentication;
      $this->campaignId = $campaignId;
      $this->deleteReports = $deleteReports;
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
     * @return DeleteCampaign
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
     * @return DeleteCampaign
     */
    public function setCampaignId($campaignId)
    {
      $this->campaignId = $campaignId;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDeleteReports()
    {
      return $this->deleteReports;
    }

    /**
     * @param boolean $deleteReports
     * @return DeleteCampaign
     */
    public function setDeleteReports($deleteReports)
    {
      $this->deleteReports = $deleteReports;
      return $this;
    }

}
