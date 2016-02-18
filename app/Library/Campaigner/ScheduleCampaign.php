<?php
namespace App\Library\Campaigner;
class ScheduleCampaign
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
     * @var boolean $sendNow
     */
    protected $sendNow = null;

    /**
     * @var CampaignScheduleData $campaignSchedule
     */
    protected $campaignSchedule = null;

    /**
     * @param Authentication $authentication
     * @param int $campaignId
     * @param boolean $sendNow
     * @param CampaignScheduleData $campaignSchedule
     */
    public function __construct($authentication, $campaignId, $sendNow, $campaignSchedule)
    {
      $this->authentication = $authentication;
      $this->campaignId = $campaignId;
      $this->sendNow = $sendNow;
      $this->campaignSchedule = $campaignSchedule;
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
     * @return ScheduleCampaign
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
     * @return ScheduleCampaign
     */
    public function setCampaignId($campaignId)
    {
      $this->campaignId = $campaignId;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getSendNow()
    {
      return $this->sendNow;
    }

    /**
     * @param boolean $sendNow
     * @return ScheduleCampaign
     */
    public function setSendNow($sendNow)
    {
      $this->sendNow = $sendNow;
      return $this;
    }

    /**
     * @return CampaignScheduleData
     */
    public function getCampaignSchedule()
    {
      return $this->campaignSchedule;
    }

    /**
     * @param CampaignScheduleData $campaignSchedule
     * @return ScheduleCampaign
     */
    public function setCampaignSchedule($campaignSchedule)
    {
      $this->campaignSchedule = $campaignSchedule;
      return $this;
    }

}
