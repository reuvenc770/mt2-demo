<?php

namespace App\Library\NetAtlantic;

class PreviewStruct
{

    /**
     * @var string $TextToMerge
     */
    protected $TextToMerge = null;

    /**
     * @var int $MemberID
     */
    protected $MemberID = null;

    /**
     * @var int $SubsetID
     */
    protected $SubsetID = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getTextToMerge()
    {
      return $this->TextToMerge;
    }

    /**
     * @param string $TextToMerge
     * @return \App\Library\NetAtlantic\PreviewStruct
     */
    public function setTextToMerge($TextToMerge)
    {
      $this->TextToMerge = $TextToMerge;
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
     * @return \App\Library\NetAtlantic\PreviewStruct
     */
    public function setMemberID($MemberID)
    {
      $this->MemberID = $MemberID;
      return $this;
    }

    /**
     * @return int
     */
    public function getSubsetID()
    {
      return $this->SubsetID;
    }

    /**
     * @param int $SubsetID
     * @return \App\Library\NetAtlantic\PreviewStruct
     */
    public function setSubsetID($SubsetID)
    {
      $this->SubsetID = $SubsetID;
      return $this;
    }

}
