<?php
namespace App\Library\Campaigner;
class EmailTemplateDescription
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
     * @var int $CategoryId
     */
    protected $CategoryId = null;

    /**
     * @var string $CategoryName
     */
    protected $CategoryName = null;

    /**
     * @var int $SubCategoryId
     */
    protected $SubCategoryId = null;

    /**
     * @var string $SubCategoryName
     */
    protected $SubCategoryName = null;

    /**
     * @var \DateTime $CreatedDate
     */
    protected $CreatedDate = null;

    /**
     * @var \DateTime $LastModifiedDate
     */
    protected $LastModifiedDate = null;

    /**
     * @var string $ThumbnailURL
     */
    protected $ThumbnailURL = null;

    /**
     * @var boolean $IsWelcomeTemplate
     */
    protected $IsWelcomeTemplate = null;

    /**
     * @var boolean $IsFeature
     */
    protected $IsFeature = null;

    /**
     * @param int $Id
     * @param string $Name
     * @param int $CategoryId
     * @param string $CategoryName
     * @param int $SubCategoryId
     * @param string $SubCategoryName
     * @param \DateTime $CreatedDate
     * @param \DateTime $LastModifiedDate
     * @param string $ThumbnailURL
     * @param boolean $IsWelcomeTemplate
     * @param boolean $IsFeature
     */
    public function __construct($Id, $Name, $CategoryId, $CategoryName, $SubCategoryId, $SubCategoryName, \DateTime $CreatedDate, \DateTime $LastModifiedDate, $ThumbnailURL, $IsWelcomeTemplate, $IsFeature)
    {
      $this->Id = $Id;
      $this->Name = $Name;
      $this->CategoryId = $CategoryId;
      $this->CategoryName = $CategoryName;
      $this->SubCategoryId = $SubCategoryId;
      $this->SubCategoryName = $SubCategoryName;
      $this->CreatedDate = $CreatedDate->format(\DateTime::ATOM);
      $this->LastModifiedDate = $LastModifiedDate->format(\DateTime::ATOM);
      $this->ThumbnailURL = $ThumbnailURL;
      $this->IsWelcomeTemplate = $IsWelcomeTemplate;
      $this->IsFeature = $IsFeature;
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
     * @return EmailTemplateDescription
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
     * @return EmailTemplateDescription
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
      return $this->CategoryId;
    }

    /**
     * @param int $CategoryId
     * @return EmailTemplateDescription
     */
    public function setCategoryId($CategoryId)
    {
      $this->CategoryId = $CategoryId;
      return $this;
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
      return $this->CategoryName;
    }

    /**
     * @param string $CategoryName
     * @return EmailTemplateDescription
     */
    public function setCategoryName($CategoryName)
    {
      $this->CategoryName = $CategoryName;
      return $this;
    }

    /**
     * @return int
     */
    public function getSubCategoryId()
    {
      return $this->SubCategoryId;
    }

    /**
     * @param int $SubCategoryId
     * @return EmailTemplateDescription
     */
    public function setSubCategoryId($SubCategoryId)
    {
      $this->SubCategoryId = $SubCategoryId;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubCategoryName()
    {
      return $this->SubCategoryName;
    }

    /**
     * @param string $SubCategoryName
     * @return EmailTemplateDescription
     */
    public function setSubCategoryName($SubCategoryName)
    {
      $this->SubCategoryName = $SubCategoryName;
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
     * @return EmailTemplateDescription
     */
    public function setCreatedDate(\DateTime $CreatedDate)
    {
      $this->CreatedDate = $CreatedDate->format(\DateTime::ATOM);
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
     * @return EmailTemplateDescription
     */
    public function setLastModifiedDate(\DateTime $LastModifiedDate)
    {
      $this->LastModifiedDate = $LastModifiedDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return string
     */
    public function getThumbnailURL()
    {
      return $this->ThumbnailURL;
    }

    /**
     * @param string $ThumbnailURL
     * @return EmailTemplateDescription
     */
    public function setThumbnailURL($ThumbnailURL)
    {
      $this->ThumbnailURL = $ThumbnailURL;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsWelcomeTemplate()
    {
      return $this->IsWelcomeTemplate;
    }

    /**
     * @param boolean $IsWelcomeTemplate
     * @return EmailTemplateDescription
     */
    public function setIsWelcomeTemplate($IsWelcomeTemplate)
    {
      $this->IsWelcomeTemplate = $IsWelcomeTemplate;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsFeature()
    {
      return $this->IsFeature;
    }

    /**
     * @param boolean $IsFeature
     * @return EmailTemplateDescription
     */
    public function setIsFeature($IsFeature)
    {
      $this->IsFeature = $IsFeature;
      return $this;
    }

}
