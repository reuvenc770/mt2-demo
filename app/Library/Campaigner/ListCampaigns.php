<?php
namespace App\Library\Campaigner;
class ListCampaigns
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var CampaignFilter $campaignFilter
     */
    protected $campaignFilter = null;

    /**
     * @var DateTimeFilter $dateTimeFilter
     */
    protected $dateTimeFilter = null;

    /**
     * @var CampaignStatus $campaignStatus
     */
    protected $campaignStatus = null;

    /**
     * @var CampaignType $campaignType
     */
    protected $campaignType = null;

    /**
     * @param Authentication $authentication
     * @param CampaignFilter $campaignFilter
     * @param DateTimeFilter $dateTimeFilter
     * @param CampaignStatus $campaignStatus
     * @param CampaignType $campaignType
     */
    public function __construct($authentication, $campaignFilter, $dateTimeFilter, $campaignStatus, $campaignType)
    {
      $this->authentication = $authentication;
      $this->campaignFilter = $campaignFilter;
      $this->dateTimeFilter = $dateTimeFilter;
      $this->campaignStatus = $campaignStatus;
      $this->campaignType = $campaignType;
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
     * @return ListCampaigns
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return CampaignFilter
     */
    public function getCampaignFilter()
    {
      return $this->campaignFilter;
    }

    /**
     * @param CampaignFilter $campaignFilter
     * @return ListCampaigns
     */
    public function setCampaignFilter($campaignFilter)
    {
      $this->campaignFilter = $campaignFilter;
      return $this;
    }

    /**
     * @return DateTimeFilter
     */
    public function getDateTimeFilter()
    {
      return $this->dateTimeFilter;
    }

    /**
     * @param DateTimeFilter $dateTimeFilter
     * @return ListCampaigns
     */
    public function setDateTimeFilter($dateTimeFilter)
    {
      $this->dateTimeFilter = $dateTimeFilter;
      return $this;
    }

    /**
     * @return CampaignStatus
     */
    public function getCampaignStatus()
    {
      return $this->campaignStatus;
    }

    /**
     * @param CampaignStatus $campaignStatus
     * @return ListCampaigns
     */
    public function setCampaignStatus($campaignStatus)
    {
      $this->campaignStatus = $campaignStatus;
      return $this;
    }

    /**
     * @return CampaignType
     */
    public function getCampaignType()
    {
      return $this->campaignType;
    }

    /**
     * @param CampaignType $campaignType
     * @return ListCampaigns
     */
    public function setCampaignType($campaignType)
    {
      $this->campaignType = $campaignType;
      return $this;
    }

}
