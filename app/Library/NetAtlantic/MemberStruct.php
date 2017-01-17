<?php

namespace App\Library\NetAtlantic;

class MemberStruct
{

    /**
     * @var string $Additional
     */
    protected $Additional = null;

    /**
     * @var MemberKindEnum $MembershipKind
     */
    protected $MembershipKind = null;

    /**
     * @var boolean $ApprovalNeeded
     */
    protected $ApprovalNeeded = null;

    /**
     * @var string $Password
     */
    protected $Password = null;

    /**
     * @var boolean $NotifyError
     */
    protected $NotifyError = null;

    /**
     * @var \DateTime $ExpireDate
     */
    protected $ExpireDate = null;

    /**
     * @var string $Comment
     */
    protected $Comment = null;

    /**
     * @var string $UserID
     */
    protected $UserID = null;

    /**
     * @var boolean $ReadsHtml
     */
    protected $ReadsHtml = null;

    /**
     * @var boolean $ReceiveAdminEmail
     */
    protected $ReceiveAdminEmail = null;

    /**
     * @var MailFormatEnum $MailFormat
     */
    protected $MailFormat = null;

    /**
     * @var \DateTime $DateConfirm
     */
    protected $DateConfirm = null;

    /**
     * @var int $NumberOfBounces
     */
    protected $NumberOfBounces = null;

    /**
     * @var int $NumApprovalsNeeded
     */
    protected $NumApprovalsNeeded = null;

    /**
     * @var boolean $NotifySubmission
     */
    protected $NotifySubmission = null;

    /**
     * @var boolean $NoRepro
     */
    protected $NoRepro = null;

    /**
     * @var int $MemberID
     */
    protected $MemberID = null;

    /**
     * @var ArrayOfKeyValueType $Demographics
     */
    protected $Demographics = null;

    /**
     * @var string $EmailAddress
     */
    protected $EmailAddress = null;

    /**
     * @var \DateTime $DateJoined
     */
    protected $DateJoined = null;

    /**
     * @var boolean $IsListAdmin
     */
    protected $IsListAdmin = null;

    /**
     * @var boolean $ReceiveAcknowlegment
     */
    protected $ReceiveAcknowlegment = null;

    /**
     * @var \DateTime $DateBounce
     */
    protected $DateBounce = null;

    /**
     * @var \DateTime $DateHeld
     */
    protected $DateHeld = null;

    /**
     * @var MemberStatusEnum $MemberStatus
     */
    protected $MemberStatus = null;

    /**
     * @var string $FullName
     */
    protected $FullName = null;

    /**
     * @var boolean $CanApprovePending
     */
    protected $CanApprovePending = null;

    /**
     * @var boolean $CleanAuto
     */
    protected $CleanAuto = null;

    /**
     * @var string $ListName
     */
    protected $ListName = null;

    /**
     * @var \DateTime $DateUnsubscribed
     */
    protected $DateUnsubscribed = null;

    
    public function __construct()
    {
    
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
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setAdditional($Additional)
    {
      $this->Additional = $Additional;
      return $this;
    }

    /**
     * @return MemberKindEnum
     */
    public function getMembershipKind()
    {
      return $this->MembershipKind;
    }

    /**
     * @param MemberKindEnum $MembershipKind
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setMembershipKind($MembershipKind)
    {
      $this->MembershipKind = $MembershipKind;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getApprovalNeeded()
    {
      return $this->ApprovalNeeded;
    }

    /**
     * @param boolean $ApprovalNeeded
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setApprovalNeeded($ApprovalNeeded)
    {
      $this->ApprovalNeeded = $ApprovalNeeded;
      return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
      return $this->Password;
    }

    /**
     * @param string $Password
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setPassword($Password)
    {
      $this->Password = $Password;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getNotifyError()
    {
      return $this->NotifyError;
    }

    /**
     * @param boolean $NotifyError
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setNotifyError($NotifyError)
    {
      $this->NotifyError = $NotifyError;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpireDate()
    {
      if ($this->ExpireDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->ExpireDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $ExpireDate
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setExpireDate(\DateTime $ExpireDate = null)
    {
      if ($ExpireDate == null) {
       $this->ExpireDate = null;
      } else {
        $this->ExpireDate = $ExpireDate->format(\DateTime::ATOM);
      }
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
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setComment($Comment)
    {
      $this->Comment = $Comment;
      return $this;
    }

    /**
     * @return string
     */
    public function getUserID()
    {
      return $this->UserID;
    }

    /**
     * @param string $UserID
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setUserID($UserID)
    {
      $this->UserID = $UserID;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getReadsHtml()
    {
      return $this->ReadsHtml;
    }

    /**
     * @param boolean $ReadsHtml
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setReadsHtml($ReadsHtml)
    {
      $this->ReadsHtml = $ReadsHtml;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getReceiveAdminEmail()
    {
      return $this->ReceiveAdminEmail;
    }

    /**
     * @param boolean $ReceiveAdminEmail
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setReceiveAdminEmail($ReceiveAdminEmail)
    {
      $this->ReceiveAdminEmail = $ReceiveAdminEmail;
      return $this;
    }

    /**
     * @return MailFormatEnum
     */
    public function getMailFormat()
    {
      return $this->MailFormat;
    }

    /**
     * @param MailFormatEnum $MailFormat
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setMailFormat($MailFormat)
    {
      $this->MailFormat = $MailFormat;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateConfirm()
    {
      if ($this->DateConfirm == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateConfirm);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateConfirm
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setDateConfirm(\DateTime $DateConfirm = null)
    {
      if ($DateConfirm == null) {
       $this->DateConfirm = null;
      } else {
        $this->DateConfirm = $DateConfirm->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfBounces()
    {
      return $this->NumberOfBounces;
    }

    /**
     * @param int $NumberOfBounces
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setNumberOfBounces($NumberOfBounces)
    {
      $this->NumberOfBounces = $NumberOfBounces;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumApprovalsNeeded()
    {
      return $this->NumApprovalsNeeded;
    }

    /**
     * @param int $NumApprovalsNeeded
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setNumApprovalsNeeded($NumApprovalsNeeded)
    {
      $this->NumApprovalsNeeded = $NumApprovalsNeeded;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getNotifySubmission()
    {
      return $this->NotifySubmission;
    }

    /**
     * @param boolean $NotifySubmission
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setNotifySubmission($NotifySubmission)
    {
      $this->NotifySubmission = $NotifySubmission;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getNoRepro()
    {
      return $this->NoRepro;
    }

    /**
     * @param boolean $NoRepro
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setNoRepro($NoRepro)
    {
      $this->NoRepro = $NoRepro;
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
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setMemberID($MemberID)
    {
      $this->MemberID = $MemberID;
      return $this;
    }

    /**
     * @return ArrayOfKeyValueType
     */
    public function getDemographics()
    {
      return $this->Demographics;
    }

    /**
     * @param ArrayOfKeyValueType $Demographics
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setDemographics($Demographics)
    {
      $this->Demographics = $Demographics;
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
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setEmailAddress($EmailAddress)
    {
      $this->EmailAddress = $EmailAddress;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateJoined()
    {
      if ($this->DateJoined == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateJoined);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateJoined
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setDateJoined(\DateTime $DateJoined = null)
    {
      if ($DateJoined == null) {
       $this->DateJoined = null;
      } else {
        $this->DateJoined = $DateJoined->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsListAdmin()
    {
      return $this->IsListAdmin;
    }

    /**
     * @param boolean $IsListAdmin
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setIsListAdmin($IsListAdmin)
    {
      $this->IsListAdmin = $IsListAdmin;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getReceiveAcknowlegment()
    {
      return $this->ReceiveAcknowlegment;
    }

    /**
     * @param boolean $ReceiveAcknowlegment
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setReceiveAcknowlegment($ReceiveAcknowlegment)
    {
      $this->ReceiveAcknowlegment = $ReceiveAcknowlegment;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateBounce()
    {
      if ($this->DateBounce == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateBounce);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateBounce
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setDateBounce(\DateTime $DateBounce = null)
    {
      if ($DateBounce == null) {
       $this->DateBounce = null;
      } else {
        $this->DateBounce = $DateBounce->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateHeld()
    {
      if ($this->DateHeld == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateHeld);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateHeld
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setDateHeld(\DateTime $DateHeld = null)
    {
      if ($DateHeld == null) {
       $this->DateHeld = null;
      } else {
        $this->DateHeld = $DateHeld->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return MemberStatusEnum
     */
    public function getMemberStatus()
    {
      return $this->MemberStatus;
    }

    /**
     * @param MemberStatusEnum $MemberStatus
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setMemberStatus($MemberStatus)
    {
      $this->MemberStatus = $MemberStatus;
      return $this;
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
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setFullName($FullName)
    {
      $this->FullName = $FullName;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getCanApprovePending()
    {
      return $this->CanApprovePending;
    }

    /**
     * @param boolean $CanApprovePending
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setCanApprovePending($CanApprovePending)
    {
      $this->CanApprovePending = $CanApprovePending;
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
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setCleanAuto($CleanAuto)
    {
      $this->CleanAuto = $CleanAuto;
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
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setListName($ListName)
    {
      $this->ListName = $ListName;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateUnsubscribed()
    {
      if ($this->DateUnsubscribed == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateUnsubscribed);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateUnsubscribed
     * @return \App\Library\NetAtlantic\MemberStruct
     */
    public function setDateUnsubscribed(\DateTime $DateUnsubscribed = null)
    {
      if ($DateUnsubscribed == null) {
       $this->DateUnsubscribed = null;
      } else {
        $this->DateUnsubscribed = $DateUnsubscribed->format(\DateTime::ATOM);
      }
      return $this;
    }

}
