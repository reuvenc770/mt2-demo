<?php

namespace App\Library\NetAtlantic;

class TMMessageStruct
{

    /**
     * @var string $RecipientEmail
     */
    protected $RecipientEmail = null;

    /**
     * @var ArrayOfKeyValueType $Payload
     */
    protected $Payload = null;

    /**
     * @var int $MailingID
     */
    protected $MailingID = null;

    /**
     * @var int $MemberID
     */
    protected $MemberID = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getRecipientEmail()
    {
      return $this->RecipientEmail;
    }

    /**
     * @param string $RecipientEmail
     * @return \App\Library\NetAtlantic\TMMessageStruct
     */
    public function setRecipientEmail($RecipientEmail)
    {
      $this->RecipientEmail = $RecipientEmail;
      return $this;
    }

    /**
     * @return ArrayOfKeyValueType
     */
    public function getPayload()
    {
      return $this->Payload;
    }

    /**
     * @param ArrayOfKeyValueType $Payload
     * @return \App\Library\NetAtlantic\TMMessageStruct
     */
    public function setPayload($Payload)
    {
      $this->Payload = $Payload;
      return $this;
    }

    /**
     * @return int
     */
    public function getMailingID()
    {
      return $this->MailingID;
    }

    /**
     * @param int $MailingID
     * @return \App\Library\NetAtlantic\TMMessageStruct
     */
    public function setMailingID($MailingID)
    {
      $this->MailingID = $MailingID;
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
     * @return \App\Library\NetAtlantic\TMMessageStruct
     */
    public function setMemberID($MemberID)
    {
      $this->MemberID = $MemberID;
      return $this;
    }

}
