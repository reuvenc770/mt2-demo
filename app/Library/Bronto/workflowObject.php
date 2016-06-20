<?php
namespace App\Library\Bronto;
class workflowObject
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var string $siteId
     */
    protected $siteId = null;

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $description
     */
    protected $description = null;

    /**
     * @var string $status
     */
    protected $status = null;

    /**
     * @var \DateTime $createdDate
     */
    protected $createdDate = null;

    /**
     * @var \DateTime $modifiedDate
     */
    protected $modifiedDate = null;

    /**
     * @var \DateTime $activatedDate
     */
    protected $activatedDate = null;

    /**
     * @var \DateTime $deActivatedDate
     */
    protected $deActivatedDate = null;

    /**
     * @param string $id
     * @param string $siteId
     * @param string $name
     * @param string $description
     * @param string $status
     * @param \DateTime $createdDate
     * @param \DateTime $modifiedDate
     * @param \DateTime $activatedDate
     * @param \DateTime $deActivatedDate
     */
    public function __construct($id, $siteId, $name, $description, $status, \DateTime $createdDate, \DateTime $modifiedDate, \DateTime $activatedDate, \DateTime $deActivatedDate)
    {
      $this->id = $id;
      $this->siteId = $siteId;
      $this->name = $name;
      $this->description = $description;
      $this->status = $status;
      $this->createdDate = $createdDate->format(\DateTime::ATOM);
      $this->modifiedDate = $modifiedDate->format(\DateTime::ATOM);
      $this->activatedDate = $activatedDate->format(\DateTime::ATOM);
      $this->deActivatedDate = $deActivatedDate->format(\DateTime::ATOM);
    }

    /**
     * @return string
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string $id
     * @return workflowObject
     */
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return string
     */
    public function getSiteId()
    {
      return $this->siteId;
    }

    /**
     * @param string $siteId
     * @return workflowObject
     */
    public function setSiteId($siteId)
    {
      $this->siteId = $siteId;
      return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return $this->name;
    }

    /**
     * @param string $name
     * @return workflowObject
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
      return $this->description;
    }

    /**
     * @param string $description
     * @return workflowObject
     */
    public function setDescription($description)
    {
      $this->description = $description;
      return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
      return $this->status;
    }

    /**
     * @param string $status
     * @return workflowObject
     */
    public function setStatus($status)
    {
      $this->status = $status;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
      if ($this->createdDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->createdDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $createdDate
     * @return workflowObject
     */
    public function setCreatedDate(\DateTime $createdDate)
    {
      $this->createdDate = $createdDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedDate()
    {
      if ($this->modifiedDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->modifiedDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $modifiedDate
     * @return workflowObject
     */
    public function setModifiedDate(\DateTime $modifiedDate)
    {
      $this->modifiedDate = $modifiedDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getActivatedDate()
    {
      if ($this->activatedDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->activatedDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $activatedDate
     * @return workflowObject
     */
    public function setActivatedDate(\DateTime $activatedDate)
    {
      $this->activatedDate = $activatedDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeActivatedDate()
    {
      if ($this->deActivatedDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->deActivatedDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $deActivatedDate
     * @return workflowObject
     */
    public function setDeActivatedDate(\DateTime $deActivatedDate)
    {
      $this->deActivatedDate = $deActivatedDate->format(\DateTime::ATOM);
      return $this;
    }

}
