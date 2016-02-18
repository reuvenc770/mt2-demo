<?php
namespace App\Library\Campaigner;
class GetCampaignRunsSummaryReport
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
     * @var boolean $groupByDomain
     */
    protected $groupByDomain = null;

    /**
     * @var DateTimeFilter $dateTimeFilter
     */
    protected $dateTimeFilter = null;

    /**
     * @param Authentication $authentication
     * @param CampaignFilter $campaignFilter
     * @param boolean $groupByDomain
     * @param DateTimeFilter $dateTimeFilter
     */
    public function __construct($authentication, $campaignFilter, $groupByDomain, $dateTimeFilter)
    {
      $this->authentication = $authentication;
      $this->campaignFilter = $campaignFilter;
      $this->groupByDomain = $groupByDomain;
      $this->dateTimeFilter = $dateTimeFilter;
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
     * @return GetCampaignRunsSummaryReport
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
     * @return GetCampaignRunsSummaryReport
     */
    public function setCampaignFilter($campaignFilter)
    {
      $this->campaignFilter = $campaignFilter;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getGroupByDomain()
    {
      return $this->groupByDomain;
    }

    /**
     * @param boolean $groupByDomain
     * @return GetCampaignRunsSummaryReport
     */
    public function setGroupByDomain($groupByDomain)
    {
      $this->groupByDomain = $groupByDomain;
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
     * @return GetCampaignRunsSummaryReport
     */
    public function setDateTimeFilter($dateTimeFilter)
    {
      $this->dateTimeFilter = $dateTimeFilter;
      return $this;
    }

}
