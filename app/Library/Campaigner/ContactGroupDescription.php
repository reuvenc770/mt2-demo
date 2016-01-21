<?php
namespace App\Library\Campaigner;
class ContactGroupDescription
{

    /**
     * @var string $Type
     */
    protected $Type = null;

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
     * @var \DateTime $DateLastUpdated
     */
    protected $DateLastUpdated = null;

    /**
     * @var boolean $IsVisibleInForms
     */
    protected $IsVisibleInForms = null;

    /**
     * @var boolean $IsTemporaryGrouping
     */
    protected $IsTemporaryGrouping = null;

    /**
     * @param string $Type
     * @param int $Id
     * @param string $Name
     * @param string $Description
     * @param \DateTime $DateLastUpdated
     * @param boolean $IsVisibleInForms
     * @param boolean $IsTemporaryGrouping
     */
    public function __construct($Type, $Id, $Name, $Description, \DateTime $DateLastUpdated, $IsVisibleInForms, $IsTemporaryGrouping)
    {
      $this->Type = $Type;
      $this->Id = $Id;
      $this->Name = $Name;
      $this->Description = $Description;
      $this->DateLastUpdated = $DateLastUpdated->format(\DateTime::ATOM);
      $this->IsVisibleInForms = $IsVisibleInForms;
      $this->IsTemporaryGrouping = $IsTemporaryGrouping;
    }

    /**
     * @return string
     */
    public function getType()
    {
      return $this->Type;
    }

    /**
     * @param string $Type
     * @return ContactGroupDescription
     */
    public function setType($Type)
    {
      $this->Type = $Type;
      return $this;
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
     * @return ContactGroupDescription
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
     * @return ContactGroupDescription
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
     * @return ContactGroupDescription
     */
    public function setDescription($Description)
    {
      $this->Description = $Description;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateLastUpdated()
    {
      if ($this->DateLastUpdated == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateLastUpdated);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateLastUpdated
     * @return ContactGroupDescription
     */
    public function setDateLastUpdated(\DateTime $DateLastUpdated)
    {
      $this->DateLastUpdated = $DateLastUpdated->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsVisibleInForms()
    {
      return $this->IsVisibleInForms;
    }

    /**
     * @param boolean $IsVisibleInForms
     * @return ContactGroupDescription
     */
    public function setIsVisibleInForms($IsVisibleInForms)
    {
      $this->IsVisibleInForms = $IsVisibleInForms;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsTemporaryGrouping()
    {
      return $this->IsTemporaryGrouping;
    }

    /**
     * @param boolean $IsTemporaryGrouping
     * @return ContactGroupDescription
     */
    public function setIsTemporaryGrouping($IsTemporaryGrouping)
    {
      $this->IsTemporaryGrouping = $IsTemporaryGrouping;
      return $this;
    }

}
