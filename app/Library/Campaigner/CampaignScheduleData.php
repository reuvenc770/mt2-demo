<?php
namespace App\Library\Campaigner;
class CampaignScheduleData
{

    /**
     * @var \DateTime $StartDate
     */
    protected $StartDate = null;

    /**
     * @var \DateTime $EndDate
     */
    protected $EndDate = null;

    /**
     * @var RecurrenceType $RecurrenceType
     */
    protected $RecurrenceType = null;

    /**
     * @var int $OccurrenceCount
     */
    protected $OccurrenceCount = null;

    /**
     * @param \DateTime $StartDate
     * @param RecurrenceType $RecurrenceType
     * @param int $OccurrenceCount
     */
    public function __construct(\DateTime $StartDate, $RecurrenceType, $OccurrenceCount)
    {
      $this->StartDate = $StartDate->format(\DateTime::ATOM);
      $this->RecurrenceType = $RecurrenceType;
      $this->OccurrenceCount = $OccurrenceCount;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
      if ($this->StartDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->StartDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $StartDate
     * @return CampaignScheduleData
     */
    public function setStartDate(\DateTime $StartDate)
    {
      $this->StartDate = $StartDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
      if ($this->EndDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->EndDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $EndDate
     * @return CampaignScheduleData
     */
    public function setEndDate(\DateTime $EndDate)
    {
      $this->EndDate = $EndDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return RecurrenceType
     */
    public function getRecurrenceType()
    {
      return $this->RecurrenceType;
    }

    /**
     * @param RecurrenceType $RecurrenceType
     * @return CampaignScheduleData
     */
    public function setRecurrenceType($RecurrenceType)
    {
      $this->RecurrenceType = $RecurrenceType;
      return $this;
    }

    /**
     * @return int
     */
    public function getOccurrenceCount()
    {
      return $this->OccurrenceCount;
    }

    /**
     * @param int $OccurrenceCount
     * @return CampaignScheduleData
     */
    public function setOccurrenceCount($OccurrenceCount)
    {
      $this->OccurrenceCount = $OccurrenceCount;
      return $this;
    }

}
