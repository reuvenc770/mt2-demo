<?php
namespace App\Library\Campaigner;
class AttributeDetails
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
     * @var string $Value
     */
    protected $Value = null;

    /**
     * @var string $DefaultValue
     */
    protected $DefaultValue = null;

    /**
     * @param int $Id
     * @param string $Name
     * @param string $Value
     * @param string $DefaultValue
     */
    public function __construct($Id, $Name, $Value, $DefaultValue)
    {
      $this->Id = $Id;
      $this->Name = $Name;
      $this->Value = $Value;
      $this->DefaultValue = $DefaultValue;
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
     * @return AttributeDetails
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
     * @return AttributeDetails
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
      return $this->Value;
    }

    /**
     * @param string $Value
     * @return AttributeDetails
     */
    public function setValue($Value)
    {
      $this->Value = $Value;
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
     * @return AttributeDetails
     */
    public function setDefaultValue($DefaultValue)
    {
      $this->DefaultValue = $DefaultValue;
      return $this;
    }

}
