<?php
namespace App\Library\Campaigner;
class ProjectDescription
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
     * @var string $Description
     */
    protected $Description = null;

    /**
     * @var \DateTime $LastModifiedDate
     */
    protected $LastModifiedDate = null;

    /**
     * @param int $Id
     * @param string $Name
     * @param string $Description
     * @param \DateTime $LastModifiedDate
     */
    public function __construct($Id, $Name, $Description, \DateTime $LastModifiedDate)
    {
      $this->Id = $Id;
      $this->Name = $Name;
      $this->Description = $Description;
      $this->LastModifiedDate = $LastModifiedDate->format(\DateTime::ATOM);
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
     * @return ProjectDescription
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
     * @return ProjectDescription
     */
    public function setName($Name)
    {
      $this->Name = $Name;
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
     * @return ProjectDescription
     */
    public function setDescription($Description)
    {
      $this->Description = $Description;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastModifiedDate()
    {
      if ($this->LastModifiedDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->LastModifiedDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $LastModifiedDate
     * @return ProjectDescription
     */
    public function setLastModifiedDate(\DateTime $LastModifiedDate)
    {
      $this->LastModifiedDate = $LastModifiedDate->format(\DateTime::ATOM);
      return $this;
    }

}
