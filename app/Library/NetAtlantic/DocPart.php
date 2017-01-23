<?php

namespace App\Library\NetAtlantic;

class DocPart
{

    /**
     * @var string $Body
     */
    protected $Body = null;

    /**
     * @var string $MimePartName
     */
    protected $MimePartName = null;

    /**
     * @var int $CharSetID
     */
    protected $CharSetID = null;

    /**
     * @var MailSectionEncodingEnum $Encoding
     */
    protected $Encoding = null;

    
    public function __construct()
    {
    
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
     * @return \App\Library\NetAtlantic\DocPart
     */
    public function setBody($Body)
    {
      $this->Body = $Body;
      return $this;
    }

    /**
     * @return string
     */
    public function getMimePartName()
    {
      return $this->MimePartName;
    }

    /**
     * @param string $MimePartName
     * @return \App\Library\NetAtlantic\DocPart
     */
    public function setMimePartName($MimePartName)
    {
      $this->MimePartName = $MimePartName;
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
     * @return \App\Library\NetAtlantic\DocPart
     */
    public function setCharSetID($CharSetID)
    {
      $this->CharSetID = $CharSetID;
      return $this;
    }

    /**
     * @return MailSectionEncodingEnum
     */
    public function getEncoding()
    {
      return $this->Encoding;
    }

    /**
     * @param MailSectionEncodingEnum $Encoding
     * @return \App\Library\NetAtlantic\DocPart
     */
    public function setEncoding($Encoding)
    {
      $this->Encoding = $Encoding;
      return $this;
    }

}
