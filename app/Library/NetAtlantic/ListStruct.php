<?php

namespace App\Library\NetAtlantic;

class ListStruct
{

    /**
     * @var string $SMTPHeaders
     */
    protected $SMTPHeaders = null;

    /**
     * @var int $ErrHold
     */
    protected $ErrHold = null;

    /**
     * @var string $Admin
     */
    protected $Admin = null;

    /**
     * @var int $MaxMembers
     */
    protected $MaxMembers = null;

    /**
     * @var int $ReferralsPerDay
     */
    protected $ReferralsPerDay = null;

    /**
     * @var boolean $DetectOpenByDefault
     */
    protected $DetectOpenByDefault = null;

    /**
     * @var string $SubscribePassword
     */
    protected $SubscribePassword = null;

    /**
     * @var string $MessageHeader
     */
    protected $MessageHeader = null;

    /**
     * @var string $TclMergeInit
     */
    protected $TclMergeInit = null;

    /**
     * @var string $ReplyTo
     */
    protected $ReplyTo = null;

    /**
     * @var boolean $ModifyHeaderDate
     */
    protected $ModifyHeaderDate = null;

    /**
     * @var string $SponsOrgID
     */
    protected $SponsOrgID = null;

    /**
     * @var string $DefaultTo
     */
    protected $DefaultTo = null;

    /**
     * @var string $RunProgAfterSub
     */
    protected $RunProgAfterSub = null;

    /**
     * @var boolean $NoListHeader
     */
    protected $NoListHeader = null;

    /**
     * @var int $ArchiveNum
     */
    protected $ArchiveNum = null;

    /**
     * @var boolean $ConfirmSubscribes
     */
    protected $ConfirmSubscribes = null;

    /**
     * @var boolean $AllowInfo
     */
    protected $AllowInfo = null;

    /**
     * @var boolean $SimpleSub
     */
    protected $SimpleSub = null;

    /**
     * @var MemberListSecurityEnum $MemberListSecurity
     */
    protected $MemberListSecurity = null;

    /**
     * @var string $RunProgAfterUnsub
     */
    protected $RunProgAfterUnsub = null;

    /**
     * @var string $RunProgBeforePosting
     */
    protected $RunProgBeforePosting = null;

    /**
     * @var PasswordRequiredEnum $PasswordRequired
     */
    protected $PasswordRequired = null;

    /**
     * @var boolean $OnlyAllowAdminSend
     */
    protected $OnlyAllowAdminSend = null;

    /**
     * @var boolean $NoEmail
     */
    protected $NoEmail = null;

    /**
     * @var int $ApproveNum
     */
    protected $ApproveNum = null;

    /**
     * @var boolean $RecencySequentialEnabled
     */
    protected $RecencySequentialEnabled = null;

    /**
     * @var string $HeaderRemove
     */
    protected $HeaderRemove = null;

    /**
     * @var boolean $RecencyTriggeredEnabled
     */
    protected $RecencyTriggeredEnabled = null;

    /**
     * @var int $PurgeExpiredInterval
     */
    protected $PurgeExpiredInterval = null;

    /**
     * @var string $RunProgBeforeSub
     */
    protected $RunProgBeforeSub = null;

    /**
     * @var NameRequiredEnum $NameRequired
     */
    protected $NameRequired = null;

    /**
     * @var string $DescLongDocID
     */
    protected $DescLongDocID = null;

    /**
     * @var string $Comment
     */
    protected $Comment = null;

    /**
     * @var string $CommentsID
     */
    protected $CommentsID = null;

    /**
     * @var int $PurgeHeldInterval
     */
    protected $PurgeHeldInterval = null;

    /**
     * @var int $PurgeUnsubInterval
     */
    protected $PurgeUnsubInterval = null;

    /**
     * @var \DateTime $DateCreated
     */
    protected $DateCreated = null;

    /**
     * @var int $AutoReleaseHour
     */
    protected $AutoReleaseHour = null;

    /**
     * @var boolean $Disabled
     */
    protected $Disabled = null;

    /**
     * @var string $DigestHeader
     */
    protected $DigestHeader = null;

    /**
     * @var boolean $RecencyWebEnabled
     */
    protected $RecencyWebEnabled = null;

    /**
     * @var boolean $DontRewriteMessageIDHeader
     */
    protected $DontRewriteMessageIDHeader = null;

    /**
     * @var AddHeadersAndFootersEnum $AddHeadersAndFooters
     */
    protected $AddHeadersAndFooters = null;

    /**
     * @var boolean $Visitors
     */
    protected $Visitors = null;

    /**
     * @var boolean $NoSearch
     */
    protected $NoSearch = null;

    /**
     * @var ArrayOfstring $SubscriptionReports
     */
    protected $SubscriptionReports = null;

    /**
     * @var boolean $NoNNTP
     */
    protected $NoNNTP = null;

    /**
     * @var int $MaxMessageSize
     */
    protected $MaxMessageSize = null;

    /**
     * @var int $PurgeReferredInterval
     */
    protected $PurgeReferredInterval = null;

    /**
     * @var boolean $MakePostsAnonymous
     */
    protected $MakePostsAnonymous = null;

    /**
     * @var string $Keywords
     */
    protected $Keywords = null;

    /**
     * @var string $Additional
     */
    protected $Additional = null;

    /**
     * @var boolean $AddListNameToSubject
     */
    protected $AddListNameToSubject = null;

    /**
     * @var LoggingLevelEnum $RecipientLoggingLevel
     */
    protected $RecipientLoggingLevel = null;

    /**
     * @var EnableScriptingEnum $EnableScripting
     */
    protected $EnableScripting = null;

    /**
     * @var string $To
     */
    protected $To = null;

    /**
     * @var string $Topic
     */
    protected $Topic = null;

    /**
     * @var string $RunProgAfterPosting
     */
    protected $RunProgAfterPosting = null;

    /**
     * @var int $CleanNotif
     */
    protected $CleanNotif = null;

    /**
     * @var ArrayOfstring $DeliveryReports
     */
    protected $DeliveryReports = null;

    /**
     * @var int $RecencyMailCount
     */
    protected $RecencyMailCount = null;

    /**
     * @var string $RunProgBeforeUnsub
     */
    protected $RunProgBeforeUnsub = null;

    /**
     * @var ModeratedEnum $Moderated
     */
    protected $Moderated = null;

    /**
     * @var boolean $AllowCrossPosting
     */
    protected $AllowCrossPosting = null;

    /**
     * @var int $MaxPostsPerUser
     */
    protected $MaxPostsPerUser = null;

    /**
     * @var ConfirmUnsubEnum $ConfirmUnsubscribes
     */
    protected $ConfirmUnsubscribes = null;

    /**
     * @var boolean $NoArchive
     */
    protected $NoArchive = null;

    /**
     * @var int $RecencyDayCount
     */
    protected $RecencyDayCount = null;

    /**
     * @var int $PurgeUnconfirmedInterval
     */
    protected $PurgeUnconfirmedInterval = null;

    /**
     * @var boolean $RemoveDuplicateCrossPostings
     */
    protected $RemoveDuplicateCrossPostings = null;

    /**
     * @var int $ArchiveDays
     */
    protected $ArchiveDays = null;

    /**
     * @var int $NotifyHeldInterval
     */
    protected $NotifyHeldInterval = null;

    /**
     * @var boolean $TrackAllUrls
     */
    protected $TrackAllUrls = null;

    /**
     * @var int $PurgeUnapprovedInterval
     */
    protected $PurgeUnapprovedInterval = null;

    /**
     * @var string $MessageFooter
     */
    protected $MessageFooter = null;

    /**
     * @var RecencyOperatorEnum $RecencyOperator
     */
    protected $RecencyOperator = null;

    /**
     * @var string $ListName
     */
    protected $ListName = null;

    /**
     * @var int $MaxQuoting
     */
    protected $MaxQuoting = null;

    /**
     * @var string $DefaultSubject
     */
    protected $DefaultSubject = null;

    /**
     * @var int $ReleasePending
     */
    protected $ReleasePending = null;

    /**
     * @var int $KeepOutmailPostings
     */
    protected $KeepOutmailPostings = null;

    /**
     * @var string $PrivApprov
     */
    protected $PrivApprov = null;

    /**
     * @var PostPasswordEnum $PostPassword
     */
    protected $PostPassword = null;

    /**
     * @var string $DefaultFrom
     */
    protected $DefaultFrom = null;

    /**
     * @var boolean $AnyoneCanPost
     */
    protected $AnyoneCanPost = null;

    /**
     * @var ScriptingLevelEnum $ScriptingLevel
     */
    protected $ScriptingLevel = null;

    /**
     * @var boolean $Child
     */
    protected $Child = null;

    /**
     * @var string $ShortDescription
     */
    protected $ShortDescription = null;

    /**
     * @var boolean $NoEmailSubscriptions
     */
    protected $NoEmailSubscriptions = null;

    /**
     * @var boolean $DetectHtmlByDefault
     */
    protected $DetectHtmlByDefault = null;

    /**
     * @var string $SMTPFrom
     */
    protected $SMTPFrom = null;

    /**
     * @var MriVisibilityEnum $MriVisibility
     */
    protected $MriVisibility = null;

    /**
     * @var int $ListID
     */
    protected $ListID = null;

    /**
     * @var boolean $BlankSubjectOk
     */
    protected $BlankSubjectOk = null;

    /**
     * @var boolean $AllowDuplicatePosts
     */
    protected $AllowDuplicatePosts = null;

    /**
     * @var boolean $RecencyEmailEnabled
     */
    protected $RecencyEmailEnabled = null;

    /**
     * @var MergeCapOverrideEnum $MergeCapOverride
     */
    protected $MergeCapOverride = null;

    /**
     * @var boolean $CleanAuto
     */
    protected $CleanAuto = null;

    /**
     * @var string $From
     */
    protected $From = null;

    /**
     * @var boolean $NoBodyOk
     */
    protected $NoBodyOk = null;

    /**
     * @var NewSubscriberPolicyEnum $NewSubscriberSecurity
     */
    protected $NewSubscriberSecurity = null;

    /**
     * @var int $MaxMessNum
     */
    protected $MaxMessNum = null;

    /**
     * @var string $DigestFooter
     */
    protected $DigestFooter = null;

    /**
     * @var string $MessageHeaderHTML
     */
    protected $MessageHeaderHTML = null;

    /**
     * @var string $MessageFooterHTML
     */
    protected $MessageFooterHTML = null;

    /**
     * @var int $MailStreamID
     */
    protected $MailStreamID = null;

    /**
     * @var int $DefaultSubsetID
     */
    protected $DefaultSubsetID = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getSMTPHeaders()
    {
      return $this->SMTPHeaders;
    }

    /**
     * @param string $SMTPHeaders
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setSMTPHeaders($SMTPHeaders)
    {
      $this->SMTPHeaders = $SMTPHeaders;
      return $this;
    }

    /**
     * @return int
     */
    public function getErrHold()
    {
      return $this->ErrHold;
    }

    /**
     * @param int $ErrHold
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setErrHold($ErrHold)
    {
      $this->ErrHold = $ErrHold;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdmin()
    {
      return $this->Admin;
    }

    /**
     * @param string $Admin
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setAdmin($Admin)
    {
      $this->Admin = $Admin;
      return $this;
    }

    /**
     * @return int
     */
    public function getMaxMembers()
    {
      return $this->MaxMembers;
    }

    /**
     * @param int $MaxMembers
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMaxMembers($MaxMembers)
    {
      $this->MaxMembers = $MaxMembers;
      return $this;
    }

    /**
     * @return int
     */
    public function getReferralsPerDay()
    {
      return $this->ReferralsPerDay;
    }

    /**
     * @param int $ReferralsPerDay
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setReferralsPerDay($ReferralsPerDay)
    {
      $this->ReferralsPerDay = $ReferralsPerDay;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDetectOpenByDefault()
    {
      return $this->DetectOpenByDefault;
    }

    /**
     * @param boolean $DetectOpenByDefault
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setDetectOpenByDefault($DetectOpenByDefault)
    {
      $this->DetectOpenByDefault = $DetectOpenByDefault;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubscribePassword()
    {
      return $this->SubscribePassword;
    }

    /**
     * @param string $SubscribePassword
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setSubscribePassword($SubscribePassword)
    {
      $this->SubscribePassword = $SubscribePassword;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageHeader()
    {
      return $this->MessageHeader;
    }

    /**
     * @param string $MessageHeader
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMessageHeader($MessageHeader)
    {
      $this->MessageHeader = $MessageHeader;
      return $this;
    }

    /**
     * @return string
     */
    public function getTclMergeInit()
    {
      return $this->TclMergeInit;
    }

    /**
     * @param string $TclMergeInit
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setTclMergeInit($TclMergeInit)
    {
      $this->TclMergeInit = $TclMergeInit;
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
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setReplyTo($ReplyTo)
    {
      $this->ReplyTo = $ReplyTo;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getModifyHeaderDate()
    {
      return $this->ModifyHeaderDate;
    }

    /**
     * @param boolean $ModifyHeaderDate
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setModifyHeaderDate($ModifyHeaderDate)
    {
      $this->ModifyHeaderDate = $ModifyHeaderDate;
      return $this;
    }

    /**
     * @return string
     */
    public function getSponsOrgID()
    {
      return $this->SponsOrgID;
    }

    /**
     * @param string $SponsOrgID
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setSponsOrgID($SponsOrgID)
    {
      $this->SponsOrgID = $SponsOrgID;
      return $this;
    }

    /**
     * @return string
     */
    public function getDefaultTo()
    {
      return $this->DefaultTo;
    }

    /**
     * @param string $DefaultTo
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setDefaultTo($DefaultTo)
    {
      $this->DefaultTo = $DefaultTo;
      return $this;
    }

    /**
     * @return string
     */
    public function getRunProgAfterSub()
    {
      return $this->RunProgAfterSub;
    }

    /**
     * @param string $RunProgAfterSub
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRunProgAfterSub($RunProgAfterSub)
    {
      $this->RunProgAfterSub = $RunProgAfterSub;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getNoListHeader()
    {
      return $this->NoListHeader;
    }

    /**
     * @param boolean $NoListHeader
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setNoListHeader($NoListHeader)
    {
      $this->NoListHeader = $NoListHeader;
      return $this;
    }

    /**
     * @return int
     */
    public function getArchiveNum()
    {
      return $this->ArchiveNum;
    }

    /**
     * @param int $ArchiveNum
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setArchiveNum($ArchiveNum)
    {
      $this->ArchiveNum = $ArchiveNum;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getConfirmSubscribes()
    {
      return $this->ConfirmSubscribes;
    }

    /**
     * @param boolean $ConfirmSubscribes
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setConfirmSubscribes($ConfirmSubscribes)
    {
      $this->ConfirmSubscribes = $ConfirmSubscribes;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAllowInfo()
    {
      return $this->AllowInfo;
    }

    /**
     * @param boolean $AllowInfo
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setAllowInfo($AllowInfo)
    {
      $this->AllowInfo = $AllowInfo;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getSimpleSub()
    {
      return $this->SimpleSub;
    }

    /**
     * @param boolean $SimpleSub
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setSimpleSub($SimpleSub)
    {
      $this->SimpleSub = $SimpleSub;
      return $this;
    }

    /**
     * @return MemberListSecurityEnum
     */
    public function getMemberListSecurity()
    {
      return $this->MemberListSecurity;
    }

    /**
     * @param MemberListSecurityEnum $MemberListSecurity
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMemberListSecurity($MemberListSecurity)
    {
      $this->MemberListSecurity = $MemberListSecurity;
      return $this;
    }

    /**
     * @return string
     */
    public function getRunProgAfterUnsub()
    {
      return $this->RunProgAfterUnsub;
    }

    /**
     * @param string $RunProgAfterUnsub
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRunProgAfterUnsub($RunProgAfterUnsub)
    {
      $this->RunProgAfterUnsub = $RunProgAfterUnsub;
      return $this;
    }

    /**
     * @return string
     */
    public function getRunProgBeforePosting()
    {
      return $this->RunProgBeforePosting;
    }

    /**
     * @param string $RunProgBeforePosting
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRunProgBeforePosting($RunProgBeforePosting)
    {
      $this->RunProgBeforePosting = $RunProgBeforePosting;
      return $this;
    }

    /**
     * @return PasswordRequiredEnum
     */
    public function getPasswordRequired()
    {
      return $this->PasswordRequired;
    }

    /**
     * @param PasswordRequiredEnum $PasswordRequired
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setPasswordRequired($PasswordRequired)
    {
      $this->PasswordRequired = $PasswordRequired;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getOnlyAllowAdminSend()
    {
      return $this->OnlyAllowAdminSend;
    }

    /**
     * @param boolean $OnlyAllowAdminSend
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setOnlyAllowAdminSend($OnlyAllowAdminSend)
    {
      $this->OnlyAllowAdminSend = $OnlyAllowAdminSend;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getNoEmail()
    {
      return $this->NoEmail;
    }

    /**
     * @param boolean $NoEmail
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setNoEmail($NoEmail)
    {
      $this->NoEmail = $NoEmail;
      return $this;
    }

    /**
     * @return int
     */
    public function getApproveNum()
    {
      return $this->ApproveNum;
    }

    /**
     * @param int $ApproveNum
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setApproveNum($ApproveNum)
    {
      $this->ApproveNum = $ApproveNum;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getRecencySequentialEnabled()
    {
      return $this->RecencySequentialEnabled;
    }

    /**
     * @param boolean $RecencySequentialEnabled
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRecencySequentialEnabled($RecencySequentialEnabled)
    {
      $this->RecencySequentialEnabled = $RecencySequentialEnabled;
      return $this;
    }

    /**
     * @return string
     */
    public function getHeaderRemove()
    {
      return $this->HeaderRemove;
    }

    /**
     * @param string $HeaderRemove
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setHeaderRemove($HeaderRemove)
    {
      $this->HeaderRemove = $HeaderRemove;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getRecencyTriggeredEnabled()
    {
      return $this->RecencyTriggeredEnabled;
    }

    /**
     * @param boolean $RecencyTriggeredEnabled
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRecencyTriggeredEnabled($RecencyTriggeredEnabled)
    {
      $this->RecencyTriggeredEnabled = $RecencyTriggeredEnabled;
      return $this;
    }

    /**
     * @return int
     */
    public function getPurgeExpiredInterval()
    {
      return $this->PurgeExpiredInterval;
    }

    /**
     * @param int $PurgeExpiredInterval
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setPurgeExpiredInterval($PurgeExpiredInterval)
    {
      $this->PurgeExpiredInterval = $PurgeExpiredInterval;
      return $this;
    }

    /**
     * @return string
     */
    public function getRunProgBeforeSub()
    {
      return $this->RunProgBeforeSub;
    }

    /**
     * @param string $RunProgBeforeSub
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRunProgBeforeSub($RunProgBeforeSub)
    {
      $this->RunProgBeforeSub = $RunProgBeforeSub;
      return $this;
    }

    /**
     * @return NameRequiredEnum
     */
    public function getNameRequired()
    {
      return $this->NameRequired;
    }

    /**
     * @param NameRequiredEnum $NameRequired
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setNameRequired($NameRequired)
    {
      $this->NameRequired = $NameRequired;
      return $this;
    }

    /**
     * @return string
     */
    public function getDescLongDocID()
    {
      return $this->DescLongDocID;
    }

    /**
     * @param string $DescLongDocID
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setDescLongDocID($DescLongDocID)
    {
      $this->DescLongDocID = $DescLongDocID;
      return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
      return $this->Comment;
    }

    /**
     * @param string $Comment
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setComment($Comment)
    {
      $this->Comment = $Comment;
      return $this;
    }

    /**
     * @return string
     */
    public function getCommentsID()
    {
      return $this->CommentsID;
    }

    /**
     * @param string $CommentsID
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setCommentsID($CommentsID)
    {
      $this->CommentsID = $CommentsID;
      return $this;
    }

    /**
     * @return int
     */
    public function getPurgeHeldInterval()
    {
      return $this->PurgeHeldInterval;
    }

    /**
     * @param int $PurgeHeldInterval
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setPurgeHeldInterval($PurgeHeldInterval)
    {
      $this->PurgeHeldInterval = $PurgeHeldInterval;
      return $this;
    }

    /**
     * @return int
     */
    public function getPurgeUnsubInterval()
    {
      return $this->PurgeUnsubInterval;
    }

    /**
     * @param int $PurgeUnsubInterval
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setPurgeUnsubInterval($PurgeUnsubInterval)
    {
      $this->PurgeUnsubInterval = $PurgeUnsubInterval;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
      if ($this->DateCreated == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateCreated);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateCreated
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setDateCreated(\DateTime $DateCreated = null)
    {
      if ($DateCreated == null) {
       $this->DateCreated = null;
      } else {
        $this->DateCreated = $DateCreated->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return int
     */
    public function getAutoReleaseHour()
    {
      return $this->AutoReleaseHour;
    }

    /**
     * @param int $AutoReleaseHour
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setAutoReleaseHour($AutoReleaseHour)
    {
      $this->AutoReleaseHour = $AutoReleaseHour;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDisabled()
    {
      return $this->Disabled;
    }

    /**
     * @param boolean $Disabled
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setDisabled($Disabled)
    {
      $this->Disabled = $Disabled;
      return $this;
    }

    /**
     * @return string
     */
    public function getDigestHeader()
    {
      return $this->DigestHeader;
    }

    /**
     * @param string $DigestHeader
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setDigestHeader($DigestHeader)
    {
      $this->DigestHeader = $DigestHeader;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getRecencyWebEnabled()
    {
      return $this->RecencyWebEnabled;
    }

    /**
     * @param boolean $RecencyWebEnabled
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRecencyWebEnabled($RecencyWebEnabled)
    {
      $this->RecencyWebEnabled = $RecencyWebEnabled;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDontRewriteMessageIDHeader()
    {
      return $this->DontRewriteMessageIDHeader;
    }

    /**
     * @param boolean $DontRewriteMessageIDHeader
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setDontRewriteMessageIDHeader($DontRewriteMessageIDHeader)
    {
      $this->DontRewriteMessageIDHeader = $DontRewriteMessageIDHeader;
      return $this;
    }

    /**
     * @return AddHeadersAndFootersEnum
     */
    public function getAddHeadersAndFooters()
    {
      return $this->AddHeadersAndFooters;
    }

    /**
     * @param AddHeadersAndFootersEnum $AddHeadersAndFooters
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setAddHeadersAndFooters($AddHeadersAndFooters)
    {
      $this->AddHeadersAndFooters = $AddHeadersAndFooters;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getVisitors()
    {
      return $this->Visitors;
    }

    /**
     * @param boolean $Visitors
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setVisitors($Visitors)
    {
      $this->Visitors = $Visitors;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getNoSearch()
    {
      return $this->NoSearch;
    }

    /**
     * @param boolean $NoSearch
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setNoSearch($NoSearch)
    {
      $this->NoSearch = $NoSearch;
      return $this;
    }

    /**
     * @return ArrayOfstring
     */
    public function getSubscriptionReports()
    {
      return $this->SubscriptionReports;
    }

    /**
     * @param ArrayOfstring $SubscriptionReports
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setSubscriptionReports($SubscriptionReports)
    {
      $this->SubscriptionReports = $SubscriptionReports;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getNoNNTP()
    {
      return $this->NoNNTP;
    }

    /**
     * @param boolean $NoNNTP
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setNoNNTP($NoNNTP)
    {
      $this->NoNNTP = $NoNNTP;
      return $this;
    }

    /**
     * @return int
     */
    public function getMaxMessageSize()
    {
      return $this->MaxMessageSize;
    }

    /**
     * @param int $MaxMessageSize
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMaxMessageSize($MaxMessageSize)
    {
      $this->MaxMessageSize = $MaxMessageSize;
      return $this;
    }

    /**
     * @return int
     */
    public function getPurgeReferredInterval()
    {
      return $this->PurgeReferredInterval;
    }

    /**
     * @param int $PurgeReferredInterval
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setPurgeReferredInterval($PurgeReferredInterval)
    {
      $this->PurgeReferredInterval = $PurgeReferredInterval;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getMakePostsAnonymous()
    {
      return $this->MakePostsAnonymous;
    }

    /**
     * @param boolean $MakePostsAnonymous
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMakePostsAnonymous($MakePostsAnonymous)
    {
      $this->MakePostsAnonymous = $MakePostsAnonymous;
      return $this;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
      return $this->Keywords;
    }

    /**
     * @param string $Keywords
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setKeywords($Keywords)
    {
      $this->Keywords = $Keywords;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdditional()
    {
      return $this->Additional;
    }

    /**
     * @param string $Additional
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setAdditional($Additional)
    {
      $this->Additional = $Additional;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAddListNameToSubject()
    {
      return $this->AddListNameToSubject;
    }

    /**
     * @param boolean $AddListNameToSubject
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setAddListNameToSubject($AddListNameToSubject)
    {
      $this->AddListNameToSubject = $AddListNameToSubject;
      return $this;
    }

    /**
     * @return LoggingLevelEnum
     */
    public function getRecipientLoggingLevel()
    {
      return $this->RecipientLoggingLevel;
    }

    /**
     * @param LoggingLevelEnum $RecipientLoggingLevel
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRecipientLoggingLevel($RecipientLoggingLevel)
    {
      $this->RecipientLoggingLevel = $RecipientLoggingLevel;
      return $this;
    }

    /**
     * @return EnableScriptingEnum
     */
    public function getEnableScripting()
    {
      return $this->EnableScripting;
    }

    /**
     * @param EnableScriptingEnum $EnableScripting
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setEnableScripting($EnableScripting)
    {
      $this->EnableScripting = $EnableScripting;
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
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setTo($To)
    {
      $this->To = $To;
      return $this;
    }

    /**
     * @return string
     */
    public function getTopic()
    {
      return $this->Topic;
    }

    /**
     * @param string $Topic
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setTopic($Topic)
    {
      $this->Topic = $Topic;
      return $this;
    }

    /**
     * @return string
     */
    public function getRunProgAfterPosting()
    {
      return $this->RunProgAfterPosting;
    }

    /**
     * @param string $RunProgAfterPosting
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRunProgAfterPosting($RunProgAfterPosting)
    {
      $this->RunProgAfterPosting = $RunProgAfterPosting;
      return $this;
    }

    /**
     * @return int
     */
    public function getCleanNotif()
    {
      return $this->CleanNotif;
    }

    /**
     * @param int $CleanNotif
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setCleanNotif($CleanNotif)
    {
      $this->CleanNotif = $CleanNotif;
      return $this;
    }

    /**
     * @return ArrayOfstring
     */
    public function getDeliveryReports()
    {
      return $this->DeliveryReports;
    }

    /**
     * @param ArrayOfstring $DeliveryReports
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setDeliveryReports($DeliveryReports)
    {
      $this->DeliveryReports = $DeliveryReports;
      return $this;
    }

    /**
     * @return int
     */
    public function getRecencyMailCount()
    {
      return $this->RecencyMailCount;
    }

    /**
     * @param int $RecencyMailCount
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRecencyMailCount($RecencyMailCount)
    {
      $this->RecencyMailCount = $RecencyMailCount;
      return $this;
    }

    /**
     * @return string
     */
    public function getRunProgBeforeUnsub()
    {
      return $this->RunProgBeforeUnsub;
    }

    /**
     * @param string $RunProgBeforeUnsub
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRunProgBeforeUnsub($RunProgBeforeUnsub)
    {
      $this->RunProgBeforeUnsub = $RunProgBeforeUnsub;
      return $this;
    }

    /**
     * @return ModeratedEnum
     */
    public function getModerated()
    {
      return $this->Moderated;
    }

    /**
     * @param ModeratedEnum $Moderated
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setModerated($Moderated)
    {
      $this->Moderated = $Moderated;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAllowCrossPosting()
    {
      return $this->AllowCrossPosting;
    }

    /**
     * @param boolean $AllowCrossPosting
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setAllowCrossPosting($AllowCrossPosting)
    {
      $this->AllowCrossPosting = $AllowCrossPosting;
      return $this;
    }

    /**
     * @return int
     */
    public function getMaxPostsPerUser()
    {
      return $this->MaxPostsPerUser;
    }

    /**
     * @param int $MaxPostsPerUser
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMaxPostsPerUser($MaxPostsPerUser)
    {
      $this->MaxPostsPerUser = $MaxPostsPerUser;
      return $this;
    }

    /**
     * @return ConfirmUnsubEnum
     */
    public function getConfirmUnsubscribes()
    {
      return $this->ConfirmUnsubscribes;
    }

    /**
     * @param ConfirmUnsubEnum $ConfirmUnsubscribes
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setConfirmUnsubscribes($ConfirmUnsubscribes)
    {
      $this->ConfirmUnsubscribes = $ConfirmUnsubscribes;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getNoArchive()
    {
      return $this->NoArchive;
    }

    /**
     * @param boolean $NoArchive
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setNoArchive($NoArchive)
    {
      $this->NoArchive = $NoArchive;
      return $this;
    }

    /**
     * @return int
     */
    public function getRecencyDayCount()
    {
      return $this->RecencyDayCount;
    }

    /**
     * @param int $RecencyDayCount
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRecencyDayCount($RecencyDayCount)
    {
      $this->RecencyDayCount = $RecencyDayCount;
      return $this;
    }

    /**
     * @return int
     */
    public function getPurgeUnconfirmedInterval()
    {
      return $this->PurgeUnconfirmedInterval;
    }

    /**
     * @param int $PurgeUnconfirmedInterval
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setPurgeUnconfirmedInterval($PurgeUnconfirmedInterval)
    {
      $this->PurgeUnconfirmedInterval = $PurgeUnconfirmedInterval;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getRemoveDuplicateCrossPostings()
    {
      return $this->RemoveDuplicateCrossPostings;
    }

    /**
     * @param boolean $RemoveDuplicateCrossPostings
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRemoveDuplicateCrossPostings($RemoveDuplicateCrossPostings)
    {
      $this->RemoveDuplicateCrossPostings = $RemoveDuplicateCrossPostings;
      return $this;
    }

    /**
     * @return int
     */
    public function getArchiveDays()
    {
      return $this->ArchiveDays;
    }

    /**
     * @param int $ArchiveDays
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setArchiveDays($ArchiveDays)
    {
      $this->ArchiveDays = $ArchiveDays;
      return $this;
    }

    /**
     * @return int
     */
    public function getNotifyHeldInterval()
    {
      return $this->NotifyHeldInterval;
    }

    /**
     * @param int $NotifyHeldInterval
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setNotifyHeldInterval($NotifyHeldInterval)
    {
      $this->NotifyHeldInterval = $NotifyHeldInterval;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getTrackAllUrls()
    {
      return $this->TrackAllUrls;
    }

    /**
     * @param boolean $TrackAllUrls
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setTrackAllUrls($TrackAllUrls)
    {
      $this->TrackAllUrls = $TrackAllUrls;
      return $this;
    }

    /**
     * @return int
     */
    public function getPurgeUnapprovedInterval()
    {
      return $this->PurgeUnapprovedInterval;
    }

    /**
     * @param int $PurgeUnapprovedInterval
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setPurgeUnapprovedInterval($PurgeUnapprovedInterval)
    {
      $this->PurgeUnapprovedInterval = $PurgeUnapprovedInterval;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageFooter()
    {
      return $this->MessageFooter;
    }

    /**
     * @param string $MessageFooter
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMessageFooter($MessageFooter)
    {
      $this->MessageFooter = $MessageFooter;
      return $this;
    }

    /**
     * @return RecencyOperatorEnum
     */
    public function getRecencyOperator()
    {
      return $this->RecencyOperator;
    }

    /**
     * @param RecencyOperatorEnum $RecencyOperator
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRecencyOperator($RecencyOperator)
    {
      $this->RecencyOperator = $RecencyOperator;
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
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setListName($ListName)
    {
      $this->ListName = $ListName;
      return $this;
    }

    /**
     * @return int
     */
    public function getMaxQuoting()
    {
      return $this->MaxQuoting;
    }

    /**
     * @param int $MaxQuoting
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMaxQuoting($MaxQuoting)
    {
      $this->MaxQuoting = $MaxQuoting;
      return $this;
    }

    /**
     * @return string
     */
    public function getDefaultSubject()
    {
      return $this->DefaultSubject;
    }

    /**
     * @param string $DefaultSubject
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setDefaultSubject($DefaultSubject)
    {
      $this->DefaultSubject = $DefaultSubject;
      return $this;
    }

    /**
     * @return int
     */
    public function getReleasePending()
    {
      return $this->ReleasePending;
    }

    /**
     * @param int $ReleasePending
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setReleasePending($ReleasePending)
    {
      $this->ReleasePending = $ReleasePending;
      return $this;
    }

    /**
     * @return int
     */
    public function getKeepOutmailPostings()
    {
      return $this->KeepOutmailPostings;
    }

    /**
     * @param int $KeepOutmailPostings
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setKeepOutmailPostings($KeepOutmailPostings)
    {
      $this->KeepOutmailPostings = $KeepOutmailPostings;
      return $this;
    }

    /**
     * @return string
     */
    public function getPrivApprov()
    {
      return $this->PrivApprov;
    }

    /**
     * @param string $PrivApprov
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setPrivApprov($PrivApprov)
    {
      $this->PrivApprov = $PrivApprov;
      return $this;
    }

    /**
     * @return PostPasswordEnum
     */
    public function getPostPassword()
    {
      return $this->PostPassword;
    }

    /**
     * @param PostPasswordEnum $PostPassword
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setPostPassword($PostPassword)
    {
      $this->PostPassword = $PostPassword;
      return $this;
    }

    /**
     * @return string
     */
    public function getDefaultFrom()
    {
      return $this->DefaultFrom;
    }

    /**
     * @param string $DefaultFrom
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setDefaultFrom($DefaultFrom)
    {
      $this->DefaultFrom = $DefaultFrom;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAnyoneCanPost()
    {
      return $this->AnyoneCanPost;
    }

    /**
     * @param boolean $AnyoneCanPost
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setAnyoneCanPost($AnyoneCanPost)
    {
      $this->AnyoneCanPost = $AnyoneCanPost;
      return $this;
    }

    /**
     * @return ScriptingLevelEnum
     */
    public function getScriptingLevel()
    {
      return $this->ScriptingLevel;
    }

    /**
     * @param ScriptingLevelEnum $ScriptingLevel
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setScriptingLevel($ScriptingLevel)
    {
      $this->ScriptingLevel = $ScriptingLevel;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getChild()
    {
      return $this->Child;
    }

    /**
     * @param boolean $Child
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setChild($Child)
    {
      $this->Child = $Child;
      return $this;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
      return $this->ShortDescription;
    }

    /**
     * @param string $ShortDescription
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setShortDescription($ShortDescription)
    {
      $this->ShortDescription = $ShortDescription;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getNoEmailSubscriptions()
    {
      return $this->NoEmailSubscriptions;
    }

    /**
     * @param boolean $NoEmailSubscriptions
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setNoEmailSubscriptions($NoEmailSubscriptions)
    {
      $this->NoEmailSubscriptions = $NoEmailSubscriptions;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDetectHtmlByDefault()
    {
      return $this->DetectHtmlByDefault;
    }

    /**
     * @param boolean $DetectHtmlByDefault
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setDetectHtmlByDefault($DetectHtmlByDefault)
    {
      $this->DetectHtmlByDefault = $DetectHtmlByDefault;
      return $this;
    }

    /**
     * @return string
     */
    public function getSMTPFrom()
    {
      return $this->SMTPFrom;
    }

    /**
     * @param string $SMTPFrom
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setSMTPFrom($SMTPFrom)
    {
      $this->SMTPFrom = $SMTPFrom;
      return $this;
    }

    /**
     * @return MriVisibilityEnum
     */
    public function getMriVisibility()
    {
      return $this->MriVisibility;
    }

    /**
     * @param MriVisibilityEnum $MriVisibility
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMriVisibility($MriVisibility)
    {
      $this->MriVisibility = $MriVisibility;
      return $this;
    }

    /**
     * @return int
     */
    public function getListID()
    {
      return $this->ListID;
    }

    /**
     * @param int $ListID
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setListID($ListID)
    {
      $this->ListID = $ListID;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getBlankSubjectOk()
    {
      return $this->BlankSubjectOk;
    }

    /**
     * @param boolean $BlankSubjectOk
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setBlankSubjectOk($BlankSubjectOk)
    {
      $this->BlankSubjectOk = $BlankSubjectOk;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAllowDuplicatePosts()
    {
      return $this->AllowDuplicatePosts;
    }

    /**
     * @param boolean $AllowDuplicatePosts
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setAllowDuplicatePosts($AllowDuplicatePosts)
    {
      $this->AllowDuplicatePosts = $AllowDuplicatePosts;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getRecencyEmailEnabled()
    {
      return $this->RecencyEmailEnabled;
    }

    /**
     * @param boolean $RecencyEmailEnabled
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setRecencyEmailEnabled($RecencyEmailEnabled)
    {
      $this->RecencyEmailEnabled = $RecencyEmailEnabled;
      return $this;
    }

    /**
     * @return MergeCapOverrideEnum
     */
    public function getMergeCapOverride()
    {
      return $this->MergeCapOverride;
    }

    /**
     * @param MergeCapOverrideEnum $MergeCapOverride
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMergeCapOverride($MergeCapOverride)
    {
      $this->MergeCapOverride = $MergeCapOverride;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getCleanAuto()
    {
      return $this->CleanAuto;
    }

    /**
     * @param boolean $CleanAuto
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setCleanAuto($CleanAuto)
    {
      $this->CleanAuto = $CleanAuto;
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
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setFrom($From)
    {
      $this->From = $From;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getNoBodyOk()
    {
      return $this->NoBodyOk;
    }

    /**
     * @param boolean $NoBodyOk
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setNoBodyOk($NoBodyOk)
    {
      $this->NoBodyOk = $NoBodyOk;
      return $this;
    }

    /**
     * @return NewSubscriberPolicyEnum
     */
    public function getNewSubscriberSecurity()
    {
      return $this->NewSubscriberSecurity;
    }

    /**
     * @param NewSubscriberPolicyEnum $NewSubscriberSecurity
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setNewSubscriberSecurity($NewSubscriberSecurity)
    {
      $this->NewSubscriberSecurity = $NewSubscriberSecurity;
      return $this;
    }

    /**
     * @return int
     */
    public function getMaxMessNum()
    {
      return $this->MaxMessNum;
    }

    /**
     * @param int $MaxMessNum
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMaxMessNum($MaxMessNum)
    {
      $this->MaxMessNum = $MaxMessNum;
      return $this;
    }

    /**
     * @return string
     */
    public function getDigestFooter()
    {
      return $this->DigestFooter;
    }

    /**
     * @param string $DigestFooter
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setDigestFooter($DigestFooter)
    {
      $this->DigestFooter = $DigestFooter;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageHeaderHTML()
    {
      return $this->MessageHeaderHTML;
    }

    /**
     * @param string $MessageHeaderHTML
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMessageHeaderHTML($MessageHeaderHTML)
    {
      $this->MessageHeaderHTML = $MessageHeaderHTML;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageFooterHTML()
    {
      return $this->MessageFooterHTML;
    }

    /**
     * @param string $MessageFooterHTML
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMessageFooterHTML($MessageFooterHTML)
    {
      $this->MessageFooterHTML = $MessageFooterHTML;
      return $this;
    }

    /**
     * @return int
     */
    public function getMailStreamID()
    {
      return $this->MailStreamID;
    }

    /**
     * @param int $MailStreamID
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setMailStreamID($MailStreamID)
    {
      $this->MailStreamID = $MailStreamID;
      return $this;
    }

    /**
     * @return int
     */
    public function getDefaultSubsetID()
    {
      return $this->DefaultSubsetID;
    }

    /**
     * @param int $DefaultSubsetID
     * @return \App\Library\NetAtlantic\ListStruct
     */
    public function setDefaultSubsetID($DefaultSubsetID)
    {
      $this->DefaultSubsetID = $DefaultSubsetID;
      return $this;
    }

}
