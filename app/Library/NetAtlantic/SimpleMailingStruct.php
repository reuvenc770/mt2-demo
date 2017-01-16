<?php

namespace App\Library\NetAtlantic;

class SimpleMailingStruct
{

    /**
     * @var string $Subject
     */
    protected $Subject = null;

    /**
     * @var boolean $IsHtmlSectionEncoded
     */
    protected $IsHtmlSectionEncoded = null;

    /**
     * @var int $HtmlSectionEncoding
     */
    protected $HtmlSectionEncoding = null;

    /**
     * @var string $HtmlMessage
     */
    protected $HtmlMessage = null;

    /**
     * @var string $To
     */
    protected $To = null;

    /**
     * @var int $CharSetID
     */
    protected $CharSetID = null;

    /**
     * @var boolean $IsTextSectionEncoded
     */
    protected $IsTextSectionEncoded = null;

    /**
     * @var int $TextSectionEncoding
     */
    protected $TextSectionEncoding = null;

    /**
     * @var string $Title
     */
    protected $Title = null;

    /**
     * @var string $TextMessage
     */
    protected $TextMessage = null;

    /**
     * @var string $Attachments
     */
    protected $Attachments = null;

    /**
     * @var string $From
     */
    protected $From = null;

    /**
     * @var string $AdditionalHeaders
     */
    protected $AdditionalHeaders = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getSubject()
    {
      return $this->Subject;
    }

    /**
     * @param string $Subject
     * @return \App\Library\NetAtlantic\SimpleMailingStruct
     */
    public function setSubject($Subject)
    {
      $this->Subject = $Subject;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsHtmlSectionEncoded()
    {
      return $this->IsHtmlSectionEncoded;
    }

    /**
     * @param boolean $IsHtmlSectionEncoded
     * @return \App\Library\NetAtlantic\SimpleMailingStruct
     */
    public function setIsHtmlSectionEncoded($IsHtmlSectionEncoded)
    {
      $this->IsHtmlSectionEncoded = $IsHtmlSectionEncoded;
      return $this;
    }

    /**
     * @return int
     */
    public function getHtmlSectionEncoding()
    {
      return $this->HtmlSectionEncoding;
    }

    /**
     * @param int $HtmlSectionEncoding
     * @return \App\Library\NetAtlantic\SimpleMailingStruct
     */
    public function setHtmlSectionEncoding($HtmlSectionEncoding)
    {
      $this->HtmlSectionEncoding = $HtmlSectionEncoding;
      return $this;
    }

    /**
     * @return string
     */
    public function getHtmlMessage()
    {
      return $this->HtmlMessage;
    }

    /**
     * @param string $HtmlMessage
     * @return \App\Library\NetAtlantic\SimpleMailingStruct
     */
    public function setHtmlMessage($HtmlMessage)
    {
      $this->HtmlMessage = $HtmlMessage;
      return $this;
    }

    /**
     * @return string
     */
    public function getTo()
    {
      return $this->To;
    }

    /**
     * @param string $To
     * @return \App\Library\NetAtlantic\SimpleMailingStruct
     */
    public function setTo($To)
    {
      $this->To = $To;
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
     * @return \App\Library\NetAtlantic\SimpleMailingStruct
     */
    public function setCharSetID($CharSetID)
    {
      $this->CharSetID = $CharSetID;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsTextSectionEncoded()
    {
      return $this->IsTextSectionEncoded;
    }

    /**
     * @param boolean $IsTextSectionEncoded
     * @return \App\Library\NetAtlantic\SimpleMailingStruct
     */
    public function setIsTextSectionEncoded($IsTextSectionEncoded)
    {
      $this->IsTextSectionEncoded = $IsTextSectionEncoded;
      return $this;
    }

    /**
     * @return int
     */
    public function getTextSectionEncoding()
    {
      return $this->TextSectionEncoding;
    }

    /**
     * @param int $TextSectionEncoding
     * @return \App\Library\NetAtlantic\SimpleMailingStruct
     */
    public function setTextSectionEncoding($TextSectionEncoding)
    {
      $this->TextSectionEncoding = $TextSectionEncoding;
      return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
      return $this->Title;
    }

    /**
     * @param string $Title
     * @return \App\Library\NetAtlantic\SimpleMailingStruct
     */
    public function setTitle($Title)
    {
      $this->Title = $Title;
      return $this;
    }

    /**
     * @return string
     */
    public function getTextMessage()
    {
      return $this->TextMessage;
    }

    /**
     * @param string $TextMessage
     * @return \App\Library\NetAtlantic\SimpleMailingStruct
     */
    public function setTextMessage($TextMessage)
    {
      $this->TextMessage = $TextMessage;
      return $this;
    }

    /**
     * @return string
     */
    public function getAttachments()
    {
      return $this->Attachments;
    }

    /**
     * @param string $Attachments
     * @return \App\Library\NetAtlantic\SimpleMailingStruct
     */
    public function setAttachments($Attachments)
    {
      $this->Attachments = $Attachments;
      return $this;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
      return $this->From;
    }

    /**
     * @param string $From
     * @return \App\Library\NetAtlantic\SimpleMailingStruct
     */
    public function setFrom($From)
    {
      $this->From = $From;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalHeaders()
    {
      return $this->AdditionalHeaders;
    }

    /**
     * @param string $AdditionalHeaders
     * @return \App\Library\NetAtlantic\SimpleMailingStruct
     */
    public function setAdditionalHeaders($AdditionalHeaders)
    {
      $this->AdditionalHeaders = $AdditionalHeaders;
      return $this;
    }

}
