<?php
namespace App\Library\Campaigner;
class WorkflowDescription
{

    /**
     * @var int $Id
     */
    protected $Id = null;

    /**
     * @var string $Name
     */
    protected $Name = null;

    /**
     * @var WorkflowStatus $Status
     */
    protected $Status = null;

    /**
     * @var string $Description
     */
    protected $Description = null;

    /**
     * @var int $ProjectId
     */
    protected $ProjectId = null;

    /**
     * @var \DateTime $CreatedDate
     */
    protected $CreatedDate = null;

    /**
     * @var \DateTime $LastModifedDate
     */
    protected $LastModifedDate = null;

    /**
     * @var \DateTime $ActivationDate
     */
    protected $ActivationDate = null;

    /**
     * @var boolean $LimitContactToOneRun
     */
    protected $LimitContactToOneRun = null;

    /**
     * @var boolean $HasDelay
     */
    protected $HasDelay = null;

    /**
     * @param int $Id
     * @param string $Name
     * @param WorkflowStatus $Status
     * @param string $Description
     * @param int $ProjectId
     * @param \DateTime $CreatedDate
     * @param \DateTime $LastModifedDate
     * @param \DateTime $ActivationDate
     * @param boolean $LimitContactToOneRun
     * @param boolean $HasDelay
     */
    public function __construct($Id, $Name, $Status, $Description, $ProjectId, \DateTime $CreatedDate, \DateTime $LastModifedDate, \DateTime $ActivationDate, $LimitContactToOneRun, $HasDelay)
    {
      $this->Id = $Id;
      $this->Name = $Name;
      $this->Status = $Status;
      $this->Description = $Description;
      $this->ProjectId = $ProjectId;
      $this->CreatedDate = $CreatedDate->format(\DateTime::ATOM);
      $this->LastModifedDate = $LastModifedDate->format(\DateTime::ATOM);
      $this->ActivationDate = $ActivationDate->format(\DateTime::ATOM);
      $this->LimitContactToOneRun = $LimitContactToOneRun;
      $this->HasDelay = $HasDelay;
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
     * @return WorkflowDescription
     */
    public function setId($Id)
    {
      $this->Id = $Id;
      return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return $this->Name;
    }

    /**
     * @param string $Name
     * @return WorkflowDescription
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return WorkflowStatus
     */
    public function getStatus()
    {
      return $this->Status;
    }

    /**
     * @param WorkflowStatus $Status
     * @return WorkflowDescription
     */
    public function setStatus($Status)
    {
      $this->Status = $Status;
      return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
      return $this->Description;
    }

    /**
     * @param string $Description
     * @return WorkflowDescription
     */
    public function setDescription($Description)
    {
      $this->Description = $Description;
      return $this;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
      return $this->ProjectId;
    }

    /**
     * @param int $ProjectId
     * @return WorkflowDescription
     */
    public function setProjectId($ProjectId)
    {
      $this->ProjectId = $ProjectId;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
      if ($this->CreatedDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->CreatedDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $CreatedDate
     * @return WorkflowDescription
     */
    public function setCreatedDate(\DateTime $CreatedDate)
    {
      $this->CreatedDate = $CreatedDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastModifedDate()
    {
      if ($this->LastModifedDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->LastModifedDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $LastModifedDate
     * @return WorkflowDescription
     */
    public function setLastModifedDate(\DateTime $LastModifedDate)
    {
      $this->LastModifedDate = $LastModifedDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getActivationDate()
    {
      if ($this->ActivationDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->ActivationDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $ActivationDate
     * @return WorkflowDescription
     */
    public function setActivationDate(\DateTime $ActivationDate)
    {
      $this->ActivationDate = $ActivationDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return boolean
     */
    public function getLimitContactToOneRun()
    {
      return $this->LimitContactToOneRun;
    }

    /**
     * @param boolean $LimitContactToOneRun
     * @return WorkflowDescription
     */
    public function setLimitContactToOneRun($LimitContactToOneRun)
    {
      $this->LimitContactToOneRun = $LimitContactToOneRun;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getHasDelay()
    {
      return $this->HasDelay;
    }

    /**
     * @param boolean $HasDelay
     * @return WorkflowDescription
     */
    public function setHasDelay($HasDelay)
    {
      $this->HasDelay = $HasDelay;
      return $this;
    }

}
