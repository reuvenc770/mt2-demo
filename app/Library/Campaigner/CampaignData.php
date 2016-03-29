<?php
namespace App\Library\Campaigner;
class CampaignData
{

    /**
     * @var int $Id
     */
    protected $Id = null;

    /**
     * @var string $CampaignName
     */
    protected $CampaignName = null;

    /**
     * @var string $CampaignSubject
     */
    protected $CampaignSubject = null;

    /**
     * @var CampaignFormat $CampaignFormat
     */
    protected $CampaignFormat = null;

    /**
     * @var CampaignStatus $CampaignStatus
     */
    protected $CampaignStatus = null;

    /**
     * @var CampaignType $CampaignType
     */
    protected $CampaignType = null;

    /**
     * @var string $HtmlContent
     */
    protected $HtmlContent = null;

    /**
     * @var string $TxtContent
     */
    protected $TxtContent = null;

    /**
     * @var string $FromName
     */
    protected $FromName = null;

    /**
     * @var int $FromEmailId
     */
    protected $FromEmailId = null;

    /**
     * @var int $ReplyEmailId
     */
    protected $ReplyEmailId = null;

    /**
     * @var boolean $TrackReplies
     */
    protected $TrackReplies = null;

    /**
     * @var int $AutoReplyMessageId
     */
    protected $AutoReplyMessageId = null;

    /**
     * @var int $ProjectId
     */
    protected $ProjectId = null;

    /**
     * @var boolean $IsWelcomeCampaign
     */
    protected $IsWelcomeCampaign = null;

    /**
     * @var \DateTime $DateModified
     */
    protected $DateModified = null;

    /**
     * @var SubscriptionSettings $SubscriptionSettings
     */
    protected $SubscriptionSettings = null;

    /**
     * @var MailingAddressSettings $MailingAddressSettings
     */
    protected $MailingAddressSettings = null;

    /**
     * @var SocialSharingSettings $SocialSharingSettings
     */
    protected $SocialSharingSettings = null;

    /**
     * @var ViewOnlineSettings $ViewOnlineSettings
     */
    protected $ViewOnlineSettings = null;

    /**
     * @var CampaignEncoding $Encoding
     */
    protected $Encoding = null;

    /**
     * @param string $CampaignName
     * @param string $CampaignSubject
     * @param CampaignFormat $CampaignFormat
     * @param string $HtmlContent
     * @param string $TxtContent
     * @param string $FromName
     * @param int $FromEmailId
     * @param int $ReplyEmailId
     * @param boolean $TrackReplies
     * @param SubscriptionSettings $SubscriptionSettings
     * @param MailingAddressSettings $MailingAddressSettings
     * @param SocialSharingSettings $SocialSharingSettings
     * @param ViewOnlineSettings $ViewOnlineSettings
     */
    public function __construct($CampaignName, $CampaignSubject, $CampaignFormat, $HtmlContent, $TxtContent, $FromName, $FromEmailId, $ReplyEmailId, $TrackReplies, $SubscriptionSettings, $MailingAddressSettings, $SocialSharingSettings, $ViewOnlineSettings)
    {
      $this->CampaignName = $CampaignName;
      $this->CampaignSubject = $CampaignSubject;
      $this->CampaignFormat = $CampaignFormat;
      $this->HtmlContent = $HtmlContent;
      $this->TxtContent = $TxtContent;
      $this->FromName = $FromName;
      $this->FromEmailId = $FromEmailId;
      $this->ReplyEmailId = $ReplyEmailId;
      $this->TrackReplies = $TrackReplies;
      $this->SubscriptionSettings = $SubscriptionSettings;
      $this->MailingAddressSettings = $MailingAddressSettings;
      $this->SocialSharingSettings = $SocialSharingSettings;
      $this->ViewOnlineSettings = $ViewOnlineSettings;
    }

    /**
     * @return int
     */
    public function getId()
    {
      return $this->Id;
    }

    /**
     * @param int $Id
     * @return CampaignData
     */
    public function setId($Id)
    {
      $this->Id = $Id;
      return $this;
    }

    /**
     * @return string
     */
    public function getCampaignName()
    {
      return $this->CampaignName;
    }

    /**
     * @param string $CampaignName
     * @return CampaignData
     */
    public function setCampaignName($CampaignName)
    {
      $this->CampaignName = $CampaignName;
      return $this;
    }

    /**
     * @return string
     */
    public function getCampaignSubject()
    {
      return $this->CampaignSubject;
    }

    /**
     * @param string $CampaignSubject
     * @return CampaignData
     */
    public function setCampaignSubject($CampaignSubject)
    {
      $this->CampaignSubject = $CampaignSubject;
      return $this;
    }

    /**
     * @return CampaignFormat
     */
    public function getCampaignFormat()
    {
      return $this->CampaignFormat;
    }

    /**
     * @param CampaignFormat $CampaignFormat
     * @return CampaignData
     */
    public function setCampaignFormat($CampaignFormat)
    {
      $this->CampaignFormat = $CampaignFormat;
      return $this;
    }

    /**
     * @return CampaignStatus
     */
    public function getCampaignStatus()
    {
      return $this->CampaignStatus;
    }

    /**
     * @param CampaignStatus $CampaignStatus
     * @return CampaignData
     */
    public function setCampaignStatus($CampaignStatus)
    {
      $this->CampaignStatus = $CampaignStatus;
      return $this;
    }

    /**
     * @return CampaignType
     */
    public function getCampaignType()
    {
      return $this->CampaignType;
    }

    /**
     * @param CampaignType $CampaignType
     * @return CampaignData
     */
    public function setCampaignType($CampaignType)
    {
      $this->CampaignType = $CampaignType;
      return $this;
    }

    /**
     * @return string
     */
    public function getHtmlContent()
    {
      return $this->HtmlContent;
    }

    /**
     * @param string $HtmlContent
     * @return CampaignData
     */
    public function setHtmlContent($HtmlContent)
    {
      $this->HtmlContent = $HtmlContent;
      return $this;
    }

    /**
     * @return string
     */
    public function getTxtContent()
    {
      return $this->TxtContent;
    }

    /**
     * @param string $TxtContent
     * @return CampaignData
     */
    public function setTxtContent($TxtContent)
    {
      $this->TxtContent = $TxtContent;
      return $this;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
      return $this->FromName;
    }

    /**
     * @param string $FromName
     * @return CampaignData
     */
    public function setFromName($FromName)
    {
      $this->FromName = $FromName;
      return $this;
    }

    /**
     * @return int
     */
    public function getFromEmailId()
    {
      return $this->FromEmailId;
    }

    /**
     * @param int $FromEmailId
     * @return CampaignData
     */
    public function setFromEmailId($FromEmailId)
    {
      $this->FromEmailId = $FromEmailId;
      return $this;
    }

    /**
     * @return int
     */
    public function getReplyEmailId()
    {
      return $this->ReplyEmailId;
    }

    /**
     * @param int $ReplyEmailId
     * @return CampaignData
     */
    public function setReplyEmailId($ReplyEmailId)
    {
      $this->ReplyEmailId = $ReplyEmailId;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getTrackReplies()
    {
      return $this->TrackReplies;
    }

    /**
     * @param boolean $TrackReplies
     * @return CampaignData
     */
    public function setTrackReplies($TrackReplies)
    {
      $this->TrackReplies = $TrackReplies;
      return $this;
    }

    /**
     * @return int
     */
    public function getAutoReplyMessageId()
    {
      return $this->AutoReplyMessageId;
    }

    /**
     * @param int $AutoReplyMessageId
     * @return CampaignData
     */
    public function setAutoReplyMessageId($AutoReplyMessageId)
    {
      $this->AutoReplyMessageId = $AutoReplyMessageId;
      return $this;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
      return $this->ProjectId;
    }

    /**
     * @param int $ProjectId
     * @return CampaignData
     */
    public function setProjectId($ProjectId)
    {
      $this->ProjectId = $ProjectId;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsWelcomeCampaign()
    {
      return $this->IsWelcomeCampaign;
    }

    /**
     * @param boolean $IsWelcomeCampaign
     * @return CampaignData
     */
    public function setIsWelcomeCampaign($IsWelcomeCampaign)
    {
      $this->IsWelcomeCampaign = $IsWelcomeCampaign;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateModified()
    {
      if ($this->DateModified == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateModified);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateModified
     * @return CampaignData
     */
    public function setDateModified(\DateTime $DateModified)
    {
      $this->DateModified = $DateModified->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return SubscriptionSettings
     */
    public function getSubscriptionSettings()
    {
      return $this->SubscriptionSettings;
    }

    /**
     * @param SubscriptionSettings $SubscriptionSettings
     * @return CampaignData
     */
    public function setSubscriptionSettings($SubscriptionSettings)
    {
      $this->SubscriptionSettings = $SubscriptionSettings;
      return $this;
    }

    /**
     * @return MailingAddressSettings
     */
    public function getMailingAddressSettings()
    {
      return $this->MailingAddressSettings;
    }

    /**
     * @param MailingAddressSettings $MailingAddressSettings
     * @return CampaignData
     */
    public function setMailingAddressSettings($MailingAddressSettings)
    {
      $this->MailingAddressSettings = $MailingAddressSettings;
      return $this;
    }

    /**
     * @return SocialSharingSettings
     */
    public function getSocialSharingSettings()
    {
      return $this->SocialSharingSettings;
    }

    /**
     * @param SocialSharingSettings $SocialSharingSettings
     * @return CampaignData
     */
    public function setSocialSharingSettings($SocialSharingSettings)
    {
      $this->SocialSharingSettings = $SocialSharingSettings;
      return $this;
    }

    /**
     * @return ViewOnlineSettings
     */
    public function getViewOnlineSettings()
    {
      return $this->ViewOnlineSettings;
    }

    /**
     * @param ViewOnlineSettings $ViewOnlineSettings
     * @return CampaignData
     */
    public function setViewOnlineSettings($ViewOnlineSettings)
    {
      $this->ViewOnlineSettings = $ViewOnlineSettings;
      return $this;
    }

    /**
     * @return CampaignEncoding
     */
    public function getEncoding()
    {
      return $this->Encoding;
    }

    /**
     * @param CampaignEncoding $Encoding
     * @return CampaignData
     */
    public function setEncoding($Encoding)
    {
      $this->Encoding = $Encoding;
      return $this;
    }

}
