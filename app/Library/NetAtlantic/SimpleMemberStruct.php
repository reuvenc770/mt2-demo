<?php

namespace App\Library\NetAtlantic;

class SimpleMemberStruct
{

    /**
     * @var string $ListName
     */
    protected $ListName = null;

    /**
     * @var int $MemberID
     */
    protected $MemberID = null;

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
    public function getListName()
    {
      return $this->ListName;
    }

    /**
     * @param string $ListName
     * @return \App\Library\NetAtlantic\SimpleMemberStruct
     */
    public function setListName($ListName)
    {
      $this->ListName = $ListName;
      return $this;
    }

    /**
     * @return int
     */
    public function getMemberID()
    {
      return $this->MemberID;
    }

    /**
     * @param int $MemberID
     * @return \App\Library\NetAtlantic\SimpleMemberStruct
     */
    public function setMemberID($MemberID)
    {
      $this->MemberID = $MemberID;
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
     * @return \App\Library\NetAtlantic\SimpleMemberStruct
     */
    public function setEmailAddress($EmailAddress)
    {
      $this->EmailAddress = $EmailAddress;
      return $this;
    }

}
