<?php

class fieldOptionObject
{

    /**
     * @var string $label
     */
    protected $label = null;

    /**
     * @var string $value
     */
    protected $value = null;

    /**
     * @var boolean $isDefault
     */
    protected $isDefault = null;

    /**
     * @param string $label
     * @param string $value
     * @param boolean $isDefault
     */
    public function __construct($label, $value, $isDefault)
    {
      $this->label = $label;
      $this->value = $value;
      $this->isDefault = $isDefault;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
      return $this->label;
    }

    /**
     * @param string $label
     * @return fieldOptionObject
     */
    public function setLabel($label)
    {
      $this->label = $label;
      return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
      return $this->value;
    }

    /**
     * @param string $value
     * @return fieldOptionObject
     */
    public function setValue($value)
    {
      $this->value = $value;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsDefault()
    {
      return $this->isDefault;
    }

    /**
     * @param boolean $isDefault
     * @return fieldOptionObject
     */
    public function setIsDefault($isDefault)
    {
      $this->isDefault = $isDefault;
      return $this;
    }

}
