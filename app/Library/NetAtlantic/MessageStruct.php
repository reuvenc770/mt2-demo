<?php

namespace App\Library\NetAtlantic;

class MessageStruct
{

    /**
     * @var ArrayOfstring $RecipientEmailsIn
     */
    protected $RecipientEmailsIn = null;

    /**
     * @var ArrayOfint $RecipientMemberIDsIn
     */
    protected $RecipientMemberIDsIn = null;

    /**
     * @var ArrayOfKeyValueType $HeadersIn
     */
    protected $HeadersIn = null;

    /**
     * @var string $Body
     */
    protected $Body = null;

    /**
     * @var int $SegmentID
     */
    protected $SegmentID = null;

    /**
     * @var string $ListName
     */
    protected $ListName = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ArrayOfstring
     */
    public function getRecipientEmailsIn()
    {
      return $this->RecipientEmailsIn;
    }

    /**
     * @param ArrayOfstring $RecipientEmailsIn
     * @return \App\Library\NetAtlantic\MessageStruct
     */
    public function setRecipientEmailsIn($RecipientEmailsIn)
    {
      $this->RecipientEmailsIn = $RecipientEmailsIn;
      return $this;
    }

    /**
     * @return ArrayOfint
     */
    public function getRecipientMemberIDsIn()
    {
      return $this->RecipientMemberIDsIn;
    }

    /**
     * @param ArrayOfint $RecipientMemberIDsIn
     * @return \App\Library\NetAtlantic\MessageStruct
     */
    public function setRecipientMemberIDsIn($RecipientMemberIDsIn)
    {
      $this->RecipientMemberIDsIn = $RecipientMemberIDsIn;
      return $this;
    }

    /**
     * @return ArrayOfKeyValueType
     */
    public function getHeadersIn()
    {
      return $this->HeadersIn;
    }

    /**
     * @param ArrayOfKeyValueType $HeadersIn
     * @return \App\Library\NetAtlantic\MessageStruct
     */
    public function setHeadersIn($HeadersIn)
    {
      $this->HeadersIn = $HeadersIn;
      return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
      return $this->Body;
    }

    /**
     * @param string $Body
     * @return \App\Library\NetAtlantic\MessageStruct
     */
    public function setBody($Body)
    {
      $this->Body = $Body;
      return $this;
    }

    /**
     * @return int
     */
    public function getSegmentID()
    {
      return $this->SegmentID;
    }

    /**
     * @param int $SegmentID
     * @return \App\Library\NetAtlantic\MessageStruct
     */
    public function setSegmentID($SegmentID)
    {
      $this->SegmentID = $SegmentID;
      return $this;
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
     * @return \App\Library\NetAtlantic\MessageStruct
     */
    public function setListName($ListName)
    {
      $this->ListName = $ListName;
      return $this;
    }

}
