<?php

namespace App\Library\NetAtlantic;

class SiteAdminStruct
{

    /**
     * @var int $AdminID
     */
    protected $AdminID = null;

    /**
     * @var string $Name
     */
    protected $Name = null;

    /**
     * @var string $EmailAddress
     */
    protected $EmailAddress = null;

    /**
     * @var string $Password
     */
    protected $Password = null;

    /**
     * @var ArrayOfstring $WhatSites
     */
    protected $WhatSites = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return int
     */
    public function getAdminID()
    {
      return $this->AdminID;
    }

    /**
     * @param int $AdminID
     * @return \App\Library\NetAtlantic\SiteAdminStruct
     */
    public function setAdminID($AdminID)
    {
      $this->AdminID = $AdminID;
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
     * @return \App\Library\NetAtlantic\SiteAdminStruct
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
      return $this->EmailAddress;
    }

    /**
     * @param string $EmailAddress
     * @return \App\Library\NetAtlantic\SiteAdminStruct
     */
    public function setEmailAddress($EmailAddress)
    {
      $this->EmailAddress = $EmailAddress;
      return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
      return $this->Password;
    }

    /**
     * @param string $Password
     * @return \App\Library\NetAtlantic\SiteAdminStruct
     */
    public function setPassword($Password)
    {
      $this->Password = $Password;
      return $this;
    }

    /**
     * @return ArrayOfstring
     */
    public function getWhatSites()
    {
      return $this->WhatSites;
    }

    /**
     * @param ArrayOfstring $WhatSites
     * @return \App\Library\NetAtlantic\SiteAdminStruct
     */
    public function setWhatSites($WhatSites)
    {
      $this->WhatSites = $WhatSites;
      return $this;
    }

}
