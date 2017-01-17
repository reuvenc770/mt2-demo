<?php

namespace App\Library\NetAtlantic;

class CharSetStruct
{

    /**
     * @var string $Description
     */
    protected $Description = null;

    /**
     * @var string $Name
     */
    protected $Name = null;

    /**
     * @var int $CharSetID
     */
    protected $CharSetID = null;

    
    public function __construct()
    {
    
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
     * @return \App\Library\NetAtlantic\CharSetStruct
     */
    public function setDescription($Description)
    {
      $this->Description = $Description;
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
     * @return \App\Library\NetAtlantic\CharSetStruct
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return int
     */
    public function getCharSetID()
    {
      return $this->CharSetID;
    }

    /**
     * @param int $CharSetID
     * @return \App\Library\NetAtlantic\CharSetStruct
     */
    public function setCharSetID($CharSetID)
    {
      $this->CharSetID = $CharSetID;
      return $this;
    }

}
