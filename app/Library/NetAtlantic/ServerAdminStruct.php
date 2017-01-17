<?php

namespace App\Library\NetAtlantic;

class ServerAdminStruct
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
     * @return \App\Library\NetAtlantic\ServerAdminStruct
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
     * @return \App\Library\NetAtlantic\ServerAdminStruct
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
     * @return \App\Library\NetAtlantic\ServerAdminStruct
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
     * @return \App\Library\NetAtlantic\ServerAdminStruct
     */
    public function setPassword($Password)
    {
      $this->Password = $Password;
      return $this;
    }

}
