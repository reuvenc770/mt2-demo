<?php
namespace App\Library\Campaigner;
class filter
{

    /**
     * @var boolean $IncludeAllDefaultAttributes
     */
    protected $IncludeAllDefaultAttributes = null;

    /**
     * @var boolean $IncludeAllCustomAttributes
     */
    protected $IncludeAllCustomAttributes = null;

    /**
     * @var boolean $IncludeAllSystemAttributes
     */
    protected $IncludeAllSystemAttributes = null;

    /**
     * @param boolean $IncludeAllDefaultAttributes
     * @param boolean $IncludeAllCustomAttributes
     * @param boolean $IncludeAllSystemAttributes
     */
    public function __construct($IncludeAllDefaultAttributes, $IncludeAllCustomAttributes, $IncludeAllSystemAttributes)
    {
      $this->IncludeAllDefaultAttributes = $IncludeAllDefaultAttributes;
      $this->IncludeAllCustomAttributes = $IncludeAllCustomAttributes;
      $this->IncludeAllSystemAttributes = $IncludeAllSystemAttributes;
    }

    /**
     * @return boolean
     */
    public function getIncludeAllDefaultAttributes()
    {
      return $this->IncludeAllDefaultAttributes;
    }

    /**
     * @param boolean $IncludeAllDefaultAttributes
     * @return filter
     */
    public function setIncludeAllDefaultAttributes($IncludeAllDefaultAttributes)
    {
      $this->IncludeAllDefaultAttributes = $IncludeAllDefaultAttributes;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeAllCustomAttributes()
    {
      return $this->IncludeAllCustomAttributes;
    }

    /**
     * @param boolean $IncludeAllCustomAttributes
     * @return filter
     */
    public function setIncludeAllCustomAttributes($IncludeAllCustomAttributes)
    {
      $this->IncludeAllCustomAttributes = $IncludeAllCustomAttributes;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeAllSystemAttributes()
    {
      return $this->IncludeAllSystemAttributes;
    }

    /**
     * @param boolean $IncludeAllSystemAttributes
     * @return filter
     */
    public function setIncludeAllSystemAttributes($IncludeAllSystemAttributes)
    {
      $this->IncludeAllSystemAttributes = $IncludeAllSystemAttributes;
      return $this;
    }

}
