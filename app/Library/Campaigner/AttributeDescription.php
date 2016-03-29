<?php
namespace App\Library\Campaigner;
class AttributeDescription
{

    /**
     * @var int $Id
     */
    protected $Id = null;

    /**
     * @var int $StaticAttributeId
     */
    protected $StaticAttributeId = null;

    /**
     * @var boolean $IsKey
     */
    protected $IsKey = null;

    /**
     * @var AttributeType $AttributeType
     */
    protected $AttributeType = null;

    /**
     * @var string $Name
     */
    protected $Name = null;

    /**
     * @var string $DefaultValue
     */
    protected $DefaultValue = null;

    /**
     * @var ContactAttributeTypeId $DataType
     */
    protected $DataType = null;

    /**
     * @var \DateTime $LastModifiedDate
     */
    protected $LastModifiedDate = null;

    /**
     * @param int $Id
     * @param int $StaticAttributeId
     * @param boolean $IsKey
     * @param AttributeType $AttributeType
     * @param string $Name
     * @param string $DefaultValue
     * @param ContactAttributeTypeId $DataType
     * @param \DateTime $LastModifiedDate
     */
    public function __construct($Id, $StaticAttributeId, $IsKey, $AttributeType, $Name, $DefaultValue, $DataType, \DateTime $LastModifiedDate)
    {
      $this->Id = $Id;
      $this->StaticAttributeId = $StaticAttributeId;
      $this->IsKey = $IsKey;
      $this->AttributeType = $AttributeType;
      $this->Name = $Name;
      $this->DefaultValue = $DefaultValue;
      $this->DataType = $DataType;
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
     * @return AttributeDescription
     */
    public function setId($Id)
    {
      $this->Id = $Id;
      return $this;
    }

    /**
     * @return int
     */
    public function getStaticAttributeId()
    {
      return $this->StaticAttributeId;
    }

    /**
     * @param int $StaticAttributeId
     * @return AttributeDescription
     */
    public function setStaticAttributeId($StaticAttributeId)
    {
      $this->StaticAttributeId = $StaticAttributeId;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsKey()
    {
      return $this->IsKey;
    }

    /**
     * @param boolean $IsKey
     * @return AttributeDescription
     */
    public function setIsKey($IsKey)
    {
      $this->IsKey = $IsKey;
      return $this;
    }

    /**
     * @return AttributeType
     */
    public function getAttributeType()
    {
      return $this->AttributeType;
    }

    /**
     * @param AttributeType $AttributeType
     * @return AttributeDescription
     */
    public function setAttributeType($AttributeType)
    {
      $this->AttributeType = $AttributeType;
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
     * @return AttributeDescription
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
      return $this->DefaultValue;
    }

    /**
     * @param string $DefaultValue
     * @return AttributeDescription
     */
    public function setDefaultValue($DefaultValue)
    {
      $this->DefaultValue = $DefaultValue;
      return $this;
    }

    /**
     * @return ContactAttributeTypeId
     */
    public function getDataType()
    {
      return $this->DataType;
    }

    /**
     * @param ContactAttributeTypeId $DataType
     * @return AttributeDescription
     */
    public function setDataType($DataType)
    {
      $this->DataType = $DataType;
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
     * @return AttributeDescription
     */
    public function setLastModifiedDate(\DateTime $LastModifiedDate)
    {
      $this->LastModifiedDate = $LastModifiedDate->format(\DateTime::ATOM);
      return $this;
    }

}
