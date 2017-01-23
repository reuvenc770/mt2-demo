<?php

namespace App\Library\NetAtlantic;

class MailingStruct
{

    /**
     * @var boolean $EnableRecency
     */
    protected $EnableRecency = null;

    /**
     * @var boolean $IsHtmlSectionEncoded
     */
    protected $IsHtmlSectionEncoded = null;

    /**
     * @var string $Subject
     */
    protected $Subject = null;

    /**
     * @var string $Campaign
     */
    protected $Campaign = null;

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
     * @var RecencyWhichEnum $RecencyWhich
     */
    protected $RecencyWhich = null;

    /**
     * @var int $ResendAfterDays
     */
    protected $ResendAfterDays = null;

    /**
     * @var int $SampleSize
     */
    protected $SampleSize = null;

    /**
     * @var int $CharSetID
     */
    protected $CharSetID = null;

    /**
     * @var string $ReplyTo
     */
    protected $ReplyTo = null;

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
     * @var boolean $TrackOpens
     */
    protected $TrackOpens = null;

    /**
     * @var int $RecencyNumberOfMailings
     */
    protected $RecencyNumberOfMailings = null;

    /**
     * @var int $RecencyDays
     */
    protected $RecencyDays = null;

    /**
     * @var boolean $BypassModeration
     */
    protected $BypassModeration = null;

    /**
     * @var string $Attachments
     */
    protected $Attachments = null;

    /**
     * @var \DateTime $DontAttemptAfterDate
     */
    protected $DontAttemptAfterDate = null;

    /**
     * @var boolean $RewriteDateWhenSent
     */
    protected $RewriteDateWhenSent = null;

    /**
     * @var string $From
     */
    protected $From = null;

    /**
     * @var string $AdditionalHeaders
     */
    protected $AdditionalHeaders = null;

    /**
     * @var string $ListName
     */
    protected $ListName = null;

    /**
     * @var boolean $DetectHtml
     */
    protected $DetectHtml = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return boolean
     */
    public function getEnableRecency()
    {
      return $this->EnableRecency;
    }

    /**
     * @param boolean $EnableRecency
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setEnableRecency($EnableRecency)
    {
      $this->EnableRecency = $EnableRecency;
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
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setIsHtmlSectionEncoded($IsHtmlSectionEncoded)
    {
      $this->IsHtmlSectionEncoded = $IsHtmlSectionEncoded;
      return $this;
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
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setSubject($Subject)
    {
      $this->Subject = $Subject;
      return $this;
    }

    /**
     * @return string
     */
    public function getCampaign()
    {
      return $this->Campaign;
    }

    /**
     * @param string $Campaign
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setCampaign($Campaign)
    {
      $this->Campaign = $Campaign;
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
     * @return \App\Library\NetAtlantic\MailingStruct
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
     * @return \App\Library\NetAtlantic\MailingStruct
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
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setTo($To)
    {
      $this->To = $To;
      return $this;
    }

    /**
     * @return RecencyWhichEnum
     */
    public function getRecencyWhich()
    {
      return $this->RecencyWhich;
    }

    /**
     * @param RecencyWhichEnum $RecencyWhich
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setRecencyWhich($RecencyWhich)
    {
      $this->RecencyWhich = $RecencyWhich;
      return $this;
    }

    /**
     * @return int
     */
    public function getResendAfterDays()
    {
      return $this->ResendAfterDays;
    }

    /**
     * @param int $ResendAfterDays
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setResendAfterDays($ResendAfterDays)
    {
      $this->ResendAfterDays = $ResendAfterDays;
      return $this;
    }

    /**
     * @return int
     */
    public function getSampleSize()
    {
      return $this->SampleSize;
    }

    /**
     * @param int $SampleSize
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setSampleSize($SampleSize)
    {
      $this->SampleSize = $SampleSize;
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
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setCharSetID($CharSetID)
    {
      $this->CharSetID = $CharSetID;
      return $this;
    }

    /**
     * @return string
     */
    public function getReplyTo()
    {
      return $this->ReplyTo;
    }

    /**
     * @param string $ReplyTo
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setReplyTo($ReplyTo)
    {
      $this->ReplyTo = $ReplyTo;
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
     * @return \App\Library\NetAtlantic\MailingStruct
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
     * @return \App\Library\NetAtlantic\MailingStruct
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
     * @return \App\Library\NetAtlantic\MailingStruct
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
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setTextMessage($TextMessage)
    {
      $this->TextMessage = $TextMessage;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getTrackOpens()
    {
      return $this->TrackOpens;
    }

    /**
     * @param boolean $TrackOpens
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setTrackOpens($TrackOpens)
    {
      $this->TrackOpens = $TrackOpens;
      return $this;
    }

    /**
     * @return int
     */
    public function getRecencyNumberOfMailings()
    {
      return $this->RecencyNumberOfMailings;
    }

    /**
     * @param int $RecencyNumberOfMailings
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setRecencyNumberOfMailings($RecencyNumberOfMailings)
    {
      $this->RecencyNumberOfMailings = $RecencyNumberOfMailings;
      return $this;
    }

    /**
     * @return int
     */
    public function getRecencyDays()
    {
      return $this->RecencyDays;
    }

    /**
     * @param int $RecencyDays
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setRecencyDays($RecencyDays)
    {
      $this->RecencyDays = $RecencyDays;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getBypassModeration()
    {
      return $this->BypassModeration;
    }

    /**
     * @param boolean $BypassModeration
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setBypassModeration($BypassModeration)
    {
      $this->BypassModeration = $BypassModeration;
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
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setAttachments($Attachments)
    {
      $this->Attachments = $Attachments;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDontAttemptAfterDate()
    {
      if ($this->DontAttemptAfterDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DontAttemptAfterDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DontAttemptAfterDate
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setDontAttemptAfterDate(\DateTime $DontAttemptAfterDate = null)
    {
      if ($DontAttemptAfterDate == null) {
       $this->DontAttemptAfterDate = null;
      } else {
        $this->DontAttemptAfterDate = $DontAttemptAfterDate->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return boolean
     */
    public function getRewriteDateWhenSent()
    {
      return $this->RewriteDateWhenSent;
    }

    /**
     * @param boolean $RewriteDateWhenSent
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setRewriteDateWhenSent($RewriteDateWhenSent)
    {
      $this->RewriteDateWhenSent = $RewriteDateWhenSent;
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
     * @return \App\Library\NetAtlantic\MailingStruct
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
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setAdditionalHeaders($AdditionalHeaders)
    {
      $this->AdditionalHeaders = $AdditionalHeaders;
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
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setListName($ListName)
    {
      $this->ListName = $ListName;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDetectHtml()
    {
      return $this->DetectHtml;
    }

    /**
     * @param boolean $DetectHtml
     * @return \App\Library\NetAtlantic\MailingStruct
     */
    public function setDetectHtml($DetectHtml)
    {
      $this->DetectHtml = $DetectHtml;
      return $this;
    }

}
