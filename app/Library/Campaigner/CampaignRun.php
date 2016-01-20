<?php
namespace App\Library\Campaigner;
class CampaignRun
{

    /**
     * @var int $Id
     */
    protected $Id = null;

    /**
     * @var \DateTime $ScheduledDate
     */
    protected $ScheduledDate = null;

    /**
     * @var \DateTime $RunDate
     */
    protected $RunDate = null;

    /**
     * @var int $ContactCount
     */
    protected $ContactCount = null;

    /**
     * @var string $Status
     */
    protected $Status = null;

    /**
     * @var ArrayOfDomain $Domains
     */
    protected $Domains = null;

    /**
     * @param int $Id
     * @param \DateTime $ScheduledDate
     * @param \DateTime $RunDate
     * @param int $ContactCount
     * @param string $Status
     * @param ArrayOfDomain $Domains
     */
    public function __construct($Id, \DateTime $ScheduledDate, \DateTime $RunDate, $ContactCount, $Status, $Domains)
    {
      $this->Id = $Id;
      $this->ScheduledDate = $ScheduledDate->format(\DateTime::ATOM);
      $this->RunDate = $RunDate->format(\DateTime::ATOM);
      $this->ContactCount = $ContactCount;
      $this->Status = $Status;
      $this->Domains = $Domains;
    }

    /**
     * @return int
     */
    public function getId()
    {
      return $this->Id;
    }

    /**
     * @param int $Id
     * @return CampaignRun
     */
    public function setId($Id)
    {
      $this->Id = $Id;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getScheduledDate()
    {
      if ($this->ScheduledDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->ScheduledDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $ScheduledDate
     * @return CampaignRun
     */
    public function setScheduledDate(\DateTime $ScheduledDate)
    {
      $this->ScheduledDate = $ScheduledDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRunDate()
    {
      if ($this->RunDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->RunDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $RunDate
     * @return CampaignRun
     */
    public function setRunDate(\DateTime $RunDate)
    {
      $this->RunDate = $RunDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return int
     */
    public function getContactCount()
    {
      return $this->ContactCount;
    }

    /**
     * @param int $ContactCount
     * @return CampaignRun
     */
    public function setContactCount($ContactCount)
    {
      $this->ContactCount = $ContactCount;
      return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
      return $this->Status;
    }

    /**
     * @param string $Status
     * @return CampaignRun
     */
    public function setStatus($Status)
    {
      $this->Status = $Status;
      return $this;
    }

    /**
     * @return ArrayOfDomain
     */
    public function getDomains()
    {
      return $this->Domains;
    }

    /**
     * @param ArrayOfDomain $Domains
     * @return CampaignRun
     */
    public function setDomains($Domains)
    {
      $this->Domains = $Domains;
      return $this;
    }

}
