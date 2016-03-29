<?php
namespace App\Library\Campaigner;
class CreateUpdateAttribute
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var int $attributeId
     */
    protected $attributeId = null;

    /**
     * @var string $attributeName
     */
    protected $attributeName = null;

    /**
     * @var ContactAttributeTypeId $attributeType
     */
    protected $attributeType = null;

    /**
     * @var string $defaultValue
     */
    protected $defaultValue = null;

    /**
     * @var boolean $clearDefault
     */
    protected $clearDefault = null;

    /**
     * @param Authentication $authentication
     * @param int $attributeId
     * @param string $attributeName
     * @param ContactAttributeTypeId $attributeType
     * @param string $defaultValue
     * @param boolean $clearDefault
     */
    public function __construct($authentication, $attributeId, $attributeName, $attributeType, $defaultValue, $clearDefault)
    {
      $this->authentication = $authentication;
      $this->attributeId = $attributeId;
      $this->attributeName = $attributeName;
      $this->attributeType = $attributeType;
      $this->defaultValue = $defaultValue;
      $this->clearDefault = $clearDefault;
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
     * @return CreateUpdateAttribute
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return int
     */
    public function getAttributeId()
    {
      return $this->attributeId;
    }

    /**
     * @param int $attributeId
     * @return CreateUpdateAttribute
     */
    public function setAttributeId($attributeId)
    {
      $this->attributeId = $attributeId;
      return $this;
    }

    /**
     * @return string
     */
    public function getAttributeName()
    {
      return $this->attributeName;
    }

    /**
     * @param string $attributeName
     * @return CreateUpdateAttribute
     */
    public function setAttributeName($attributeName)
    {
      $this->attributeName = $attributeName;
      return $this;
    }

    /**
     * @return ContactAttributeTypeId
     */
    public function getAttributeType()
    {
      return $this->attributeType;
    }

    /**
     * @param ContactAttributeTypeId $attributeType
     * @return CreateUpdateAttribute
     */
    public function setAttributeType($attributeType)
    {
      $this->attributeType = $attributeType;
      return $this;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
      return $this->defaultValue;
    }

    /**
     * @param string $defaultValue
     * @return CreateUpdateAttribute
     */
    public function setDefaultValue($defaultValue)
    {
      $this->defaultValue = $defaultValue;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getClearDefault()
    {
      return $this->clearDefault;
    }

    /**
     * @param boolean $clearDefault
     * @return CreateUpdateAttribute
     */
    public function setClearDefault($clearDefault)
    {
      $this->clearDefault = $clearDefault;
      return $this;
    }

}
