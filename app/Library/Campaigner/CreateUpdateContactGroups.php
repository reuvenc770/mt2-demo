<?php
namespace App\Library\Campaigner;
class CreateUpdateContactGroups
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var ContactGroupType $contactGroupType
     */
    protected $contactGroupType = null;

    /**
     * @var int $contactGroupId
     */
    protected $contactGroupId = null;

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $description
     */
    protected $description = null;

    /**
     * @var string $xmlContactQuery
     */
    protected $xmlContactQuery = null;

    /**
     * @var SamplingType $samplingType
     */
    protected $samplingType = null;

    /**
     * @var int $sampleSize
     */
    protected $sampleSize = null;

    /**
     * @var boolean $isGroupVisible
     */
    protected $isGroupVisible = null;

    /**
     * @var boolean $isTempGroup
     */
    protected $isTempGroup = null;

    /**
     * @param Authentication $authentication
     * @param ContactGroupType $contactGroupType
     * @param int $contactGroupId
     * @param string $name
     * @param string $description
     * @param string $xmlContactQuery
     * @param SamplingType $samplingType
     * @param int $sampleSize
     * @param boolean $isGroupVisible
     * @param boolean $isTempGroup
     */
    public function __construct($authentication, $contactGroupType, $contactGroupId, $name, $description, $xmlContactQuery, $samplingType, $sampleSize, $isGroupVisible, $isTempGroup)
    {
      $this->authentication = $authentication;
      $this->contactGroupType = $contactGroupType;
      $this->contactGroupId = $contactGroupId;
      $this->name = $name;
      $this->description = $description;
      $this->xmlContactQuery = $xmlContactQuery;
      $this->samplingType = $samplingType;
      $this->sampleSize = $sampleSize;
      $this->isGroupVisible = $isGroupVisible;
      $this->isTempGroup = $isTempGroup;
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
     * @return CreateUpdateContactGroups
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return ContactGroupType
     */
    public function getContactGroupType()
    {
      return $this->contactGroupType;
    }

    /**
     * @param ContactGroupType $contactGroupType
     * @return CreateUpdateContactGroups
     */
    public function setContactGroupType($contactGroupType)
    {
      $this->contactGroupType = $contactGroupType;
      return $this;
    }

    /**
     * @return int
     */
    public function getContactGroupId()
    {
      return $this->contactGroupId;
    }

    /**
     * @param int $contactGroupId
     * @return CreateUpdateContactGroups
     */
    public function setContactGroupId($contactGroupId)
    {
      $this->contactGroupId = $contactGroupId;
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
     * @return CreateUpdateContactGroups
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
     * @return CreateUpdateContactGroups
     */
    public function setDescription($description)
    {
      $this->description = $description;
      return $this;
    }

    /**
     * @return string
     */
    public function getXmlContactQuery()
    {
      return $this->xmlContactQuery;
    }

    /**
     * @param string $xmlContactQuery
     * @return CreateUpdateContactGroups
     */
    public function setXmlContactQuery($xmlContactQuery)
    {
      $this->xmlContactQuery = $xmlContactQuery;
      return $this;
    }

    /**
     * @return SamplingType
     */
    public function getSamplingType()
    {
      return $this->samplingType;
    }

    /**
     * @param SamplingType $samplingType
     * @return CreateUpdateContactGroups
     */
    public function setSamplingType($samplingType)
    {
      $this->samplingType = $samplingType;
      return $this;
    }

    /**
     * @return int
     */
    public function getSampleSize()
    {
      return $this->sampleSize;
    }

    /**
     * @param int $sampleSize
     * @return CreateUpdateContactGroups
     */
    public function setSampleSize($sampleSize)
    {
      $this->sampleSize = $sampleSize;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsGroupVisible()
    {
      return $this->isGroupVisible;
    }

    /**
     * @param boolean $isGroupVisible
     * @return CreateUpdateContactGroups
     */
    public function setIsGroupVisible($isGroupVisible)
    {
      $this->isGroupVisible = $isGroupVisible;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsTempGroup()
    {
      return $this->isTempGroup;
    }

    /**
     * @param boolean $isTempGroup
     * @return CreateUpdateContactGroups
     */
    public function setIsTempGroup($isTempGroup)
    {
      $this->isTempGroup = $isTempGroup;
      return $this;
    }

}
