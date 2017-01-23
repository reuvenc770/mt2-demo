<?php

namespace App\Library\NetAtlantic;

class KeyValueType
{

    /**
     * @var string $Value
     */
    protected $Value = null;

    /**
     * @var string $Name
     */
    protected $Name = null;

    
    public function __construct()
    {
    
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
     * @return \App\Library\NetAtlantic\KeyValueType
     */
    public function setValue($Value)
    {
      $this->Value = $Value;
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
     * @return \App\Library\NetAtlantic\KeyValueType
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

}
