<?php
namespace App\Library\Campaigner;
class CampaignSummary
{

    /**
     * @var CampaignData $CampaignData
     */
    protected $CampaignData = null;

    /**
     * @var CampaignRecipientsData $CampaignRecipientsData
     */
    protected $CampaignRecipientsData = null;

    /**
     * @var CampaignScheduleData $CampaignScheduleData
     */
    protected $CampaignScheduleData = null;

    /**
     * @param CampaignData $CampaignData
     * @param CampaignRecipientsData $CampaignRecipientsData
     * @param CampaignScheduleData $CampaignScheduleData
     */
    public function __construct($CampaignData, $CampaignRecipientsData, $CampaignScheduleData)
    {
      $this->CampaignData = $CampaignData;
      $this->CampaignRecipientsData = $CampaignRecipientsData;
      $this->CampaignScheduleData = $CampaignScheduleData;
    }

    /**
     * @return CampaignData
     */
    public function getCampaignData()
    {
      return $this->CampaignData;
    }

    /**
     * @param CampaignData $CampaignData
     * @return CampaignSummary
     */
    public function setCampaignData($CampaignData)
    {
      $this->CampaignData = $CampaignData;
      return $this;
    }

    /**
     * @return CampaignRecipientsData
     */
    public function getCampaignRecipientsData()
    {
      return $this->CampaignRecipientsData;
    }

    /**
     * @param CampaignRecipientsData $CampaignRecipientsData
     * @return CampaignSummary
     */
    public function setCampaignRecipientsData($CampaignRecipientsData)
    {
      $this->CampaignRecipientsData = $CampaignRecipientsData;
      return $this;
    }

    /**
     * @return CampaignScheduleData
     */
    public function getCampaignScheduleData()
    {
      return $this->CampaignScheduleData;
    }

    /**
     * @param CampaignScheduleData $CampaignScheduleData
     * @return CampaignSummary
     */
    public function setCampaignScheduleData($CampaignScheduleData)
    {
      $this->CampaignScheduleData = $CampaignScheduleData;
      return $this;
    }

}
