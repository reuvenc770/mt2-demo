<?php

namespace App\Library\NetAtlantic;

class TinyMemberStruct
{

    /**
     * @var string $FullName
     */
    protected $FullName = null;

    /**
     * @var string $EmailAddress
     */
    protected $EmailAddress = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getFullName()
    {
      return $this->FullName;
    }

    /**
     * @param string $FullName
     * @return \App\Library\NetAtlantic\TinyMemberStruct
     */
    public function setFullName($FullName)
    {
      $this->FullName = $FullName;
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
     * @return \App\Library\NetAtlantic\TinyMemberStruct
     */
    public function setEmailAddress($EmailAddress)
    {
      $this->EmailAddress = $EmailAddress;
      return $this;
    }

}
