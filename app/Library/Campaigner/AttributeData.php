<?php
namespace App\Library\Campaigner;
class AttributeData
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
     * @var int $StaticAttributeId
     */
    protected $StaticAttributeId = null;

    /**
     * @var boolean $IsContactUniqueIdentifier
     */
    protected $IsContactUniqueIdentifier = null;

    /**
     * @var string $AttributeType
     */
    protected $AttributeType = null;

    /**
     * @var string $DataType
     */
    protected $DataType = null;

    /**
     * @var \DateTime $DateUpdated
     */
    protected $DateUpdated = null;

    /**
     * @var string $DefaultValue
     */
    protected $DefaultValue = null;

    /**
     * @var FormField $FormField
     */
    protected $FormField = null;

    /**
     * @param int $Id
     * @param string $Name
     * @param int $StaticAttributeId
     * @param boolean $IsContactUniqueIdentifier
     * @param string $AttributeType
     * @param string $DataType
     * @param \DateTime $DateUpdated
     * @param string $DefaultValue
     * @param FormField $FormField
     */
    public function __construct($Id, $Name, $StaticAttributeId, $IsContactUniqueIdentifier, $AttributeType, $DataType, \DateTime $DateUpdated, $DefaultValue, $FormField)
    {
      $this->Id = $Id;
      $this->Name = $Name;
      $this->StaticAttributeId = $StaticAttributeId;
      $this->IsContactUniqueIdentifier = $IsContactUniqueIdentifier;
      $this->AttributeType = $AttributeType;
      $this->DataType = $DataType;
      $this->DateUpdated = $DateUpdated->format(\DateTime::ATOM);
      $this->DefaultValue = $DefaultValue;
      $this->FormField = $FormField;
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
     * @return AttributeData
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
     * @return AttributeData
     */
    public function setName($Name)
    {
      $this->Name = $Name;
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
     * @return AttributeData
     */
    public function setStaticAttributeId($StaticAttributeId)
    {
      $this->StaticAttributeId = $StaticAttributeId;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsContactUniqueIdentifier()
    {
      return $this->IsContactUniqueIdentifier;
    }

    /**
     * @param boolean $IsContactUniqueIdentifier
     * @return AttributeData
     */
    public function setIsContactUniqueIdentifier($IsContactUniqueIdentifier)
    {
      $this->IsContactUniqueIdentifier = $IsContactUniqueIdentifier;
      return $this;
    }

    /**
     * @return string
     */
    public function getAttributeType()
    {
      return $this->AttributeType;
    }

    /**
     * @param string $AttributeType
     * @return AttributeData
     */
    public function setAttributeType($AttributeType)
    {
      $this->AttributeType = $AttributeType;
      return $this;
    }

    /**
     * @return string
     */
    public function getDataType()
    {
      return $this->DataType;
    }

    /**
     * @param string $DataType
     * @return AttributeData
     */
    public function setDataType($DataType)
    {
      $this->DataType = $DataType;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
      if ($this->DateUpdated == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateUpdated);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateUpdated
     * @return AttributeData
     */
    public function setDateUpdated(\DateTime $DateUpdated)
    {
      $this->DateUpdated = $DateUpdated->format(\DateTime::ATOM);
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
     * @return AttributeData
     */
    public function setDefaultValue($DefaultValue)
    {
      $this->DefaultValue = $DefaultValue;
      return $this;
    }

    /**
     * @return FormField
     */
    public function getFormField()
    {
      return $this->FormField;
    }

    /**
     * @param FormField $FormField
     * @return AttributeData
     */
    public function setFormField($FormField)
    {
      $this->FormField = $FormField;
      return $this;
    }

}
