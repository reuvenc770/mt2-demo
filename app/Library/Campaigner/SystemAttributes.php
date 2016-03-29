<?php
namespace App\Library\Campaigner;
class SystemAttributes
{

    /**
     * @var string $Status
     */
    protected $Status = null;

    /**
     * @var int $EngagementScore
     */
    protected $EngagementScore = null;

    /**
     * @var int $Rating
     */
    protected $Rating = null;

    /**
     * @var \DateTime $DateCreated
     */
    protected $DateCreated = null;

    /**
     * @var string $CreationMethod
     */
    protected $CreationMethod = null;

    /**
     * @var \DateTime $DateConfirmed
     */
    protected $DateConfirmed = null;

    /**
     * @var \DateTime $DateModified
     */
    protected $DateModified = null;

    /**
     * @var string $OptInURL
     */
    protected $OptInURL = null;

    /**
     * @var string $OptinIpAddress
     */
    protected $OptinIpAddress = null;

    /**
     * @var \DateTime $DateLastOpened
     */
    protected $DateLastOpened = null;

    /**
     * @var \DateTime $DateLastClicked
     */
    protected $DateLastClicked = null;

    /**
     * @var \DateTime $DateLastSentTo
     */
    protected $DateLastSentTo = null;

    /**
     * @var \DateTime $DateLastUnsubscribed
     */
    protected $DateLastUnsubscribed = null;

    /**
     * @var string $IPLastUnsubscribed
     */
    protected $IPLastUnsubscribed = null;

    /**
     * @var int $AccountId
     */
    protected $AccountId = null;

    /**
     * @var boolean $IsHBOnUpload
     */
    protected $IsHBOnUpload = null;

    /**
     * @var string $LastBounceReason
     */
    protected $LastBounceReason = null;

    /**
     * @var string $LastUnsubscribeMethod
     */
    protected $LastUnsubscribeMethod = null;

    /**
     * @var string $UnsubscribePreviousStatus
     */
    protected $UnsubscribePreviousStatus = null;

    /**
     * @var string $OwnerEmail
     */
    protected $OwnerEmail = null;

    /**
     * @var string $OwnerFirstName
     */
    protected $OwnerFirstName = null;

    /**
     * @var string $OwnerLastName
     */
    protected $OwnerLastName = null;

    /**
     * @var boolean $isLead
     */
    protected $isLead = null;

    /**
     * @param string $Status
     * @param int $EngagementScore
     * @param int $Rating
     * @param \DateTime $DateCreated
     * @param string $CreationMethod
     * @param \DateTime $DateConfirmed
     * @param \DateTime $DateModified
     * @param string $OptInURL
     * @param string $OptinIpAddress
     * @param \DateTime $DateLastOpened
     * @param \DateTime $DateLastClicked
     * @param \DateTime $DateLastSentTo
     * @param \DateTime $DateLastUnsubscribed
     * @param string $IPLastUnsubscribed
     * @param int $AccountId
     * @param boolean $IsHBOnUpload
     * @param string $LastBounceReason
     * @param string $LastUnsubscribeMethod
     * @param string $UnsubscribePreviousStatus
     * @param string $OwnerEmail
     * @param string $OwnerFirstName
     * @param string $OwnerLastName
     * @param boolean $isLead
     */
    public function __construct($Status, $EngagementScore, $Rating, \DateTime $DateCreated, $CreationMethod, \DateTime $DateConfirmed, \DateTime $DateModified, $OptInURL, $OptinIpAddress, \DateTime $DateLastOpened, \DateTime $DateLastClicked, \DateTime $DateLastSentTo, \DateTime $DateLastUnsubscribed, $IPLastUnsubscribed, $AccountId, $IsHBOnUpload, $LastBounceReason, $LastUnsubscribeMethod, $UnsubscribePreviousStatus, $OwnerEmail, $OwnerFirstName, $OwnerLastName, $isLead)
    {
      $this->Status = $Status;
      $this->EngagementScore = $EngagementScore;
      $this->Rating = $Rating;
      $this->DateCreated = $DateCreated->format(\DateTime::ATOM);
      $this->CreationMethod = $CreationMethod;
      $this->DateConfirmed = $DateConfirmed->format(\DateTime::ATOM);
      $this->DateModified = $DateModified->format(\DateTime::ATOM);
      $this->OptInURL = $OptInURL;
      $this->OptinIpAddress = $OptinIpAddress;
      $this->DateLastOpened = $DateLastOpened->format(\DateTime::ATOM);
      $this->DateLastClicked = $DateLastClicked->format(\DateTime::ATOM);
      $this->DateLastSentTo = $DateLastSentTo->format(\DateTime::ATOM);
      $this->DateLastUnsubscribed = $DateLastUnsubscribed->format(\DateTime::ATOM);
      $this->IPLastUnsubscribed = $IPLastUnsubscribed;
      $this->AccountId = $AccountId;
      $this->IsHBOnUpload = $IsHBOnUpload;
      $this->LastBounceReason = $LastBounceReason;
      $this->LastUnsubscribeMethod = $LastUnsubscribeMethod;
      $this->UnsubscribePreviousStatus = $UnsubscribePreviousStatus;
      $this->OwnerEmail = $OwnerEmail;
      $this->OwnerFirstName = $OwnerFirstName;
      $this->OwnerLastName = $OwnerLastName;
      $this->isLead = $isLead;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
      return $this->Status;
    }

    /**
     * @param string $Status
     * @return SystemAttributes
     */
    public function setStatus($Status)
    {
      $this->Status = $Status;
      return $this;
    }

    /**
     * @return int
     */
    public function getEngagementScore()
    {
      return $this->EngagementScore;
    }

    /**
     * @param int $EngagementScore
     * @return SystemAttributes
     */
    public function setEngagementScore($EngagementScore)
    {
      $this->EngagementScore = $EngagementScore;
      return $this;
    }

    /**
     * @return int
     */
    public function getRating()
    {
      return $this->Rating;
    }

    /**
     * @param int $Rating
     * @return SystemAttributes
     */
    public function setRating($Rating)
    {
      $this->Rating = $Rating;
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
     * @return SystemAttributes
     */
    public function setDateCreated(\DateTime $DateCreated)
    {
      $this->DateCreated = $DateCreated->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return string
     */
    public function getCreationMethod()
    {
      return $this->CreationMethod;
    }

    /**
     * @param string $CreationMethod
     * @return SystemAttributes
     */
    public function setCreationMethod($CreationMethod)
    {
      $this->CreationMethod = $CreationMethod;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateConfirmed()
    {
      if ($this->DateConfirmed == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateConfirmed);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateConfirmed
     * @return SystemAttributes
     */
    public function setDateConfirmed(\DateTime $DateConfirmed)
    {
      $this->DateConfirmed = $DateConfirmed->format(\DateTime::ATOM);
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
     * @return SystemAttributes
     */
    public function setDateModified(\DateTime $DateModified)
    {
      $this->DateModified = $DateModified->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return string
     */
    public function getOptInURL()
    {
      return $this->OptInURL;
    }

    /**
     * @param string $OptInURL
     * @return SystemAttributes
     */
    public function setOptInURL($OptInURL)
    {
      $this->OptInURL = $OptInURL;
      return $this;
    }

    /**
     * @return string
     */
    public function getOptinIpAddress()
    {
      return $this->OptinIpAddress;
    }

    /**
     * @param string $OptinIpAddress
     * @return SystemAttributes
     */
    public function setOptinIpAddress($OptinIpAddress)
    {
      $this->OptinIpAddress = $OptinIpAddress;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateLastOpened()
    {
      if ($this->DateLastOpened == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateLastOpened);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateLastOpened
     * @return SystemAttributes
     */
    public function setDateLastOpened(\DateTime $DateLastOpened)
    {
      $this->DateLastOpened = $DateLastOpened->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateLastClicked()
    {
      if ($this->DateLastClicked == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateLastClicked);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateLastClicked
     * @return SystemAttributes
     */
    public function setDateLastClicked(\DateTime $DateLastClicked)
    {
      $this->DateLastClicked = $DateLastClicked->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateLastSentTo()
    {
      if ($this->DateLastSentTo == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateLastSentTo);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateLastSentTo
     * @return SystemAttributes
     */
    public function setDateLastSentTo(\DateTime $DateLastSentTo)
    {
      $this->DateLastSentTo = $DateLastSentTo->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateLastUnsubscribed()
    {
      if ($this->DateLastUnsubscribed == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateLastUnsubscribed);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateLastUnsubscribed
     * @return SystemAttributes
     */
    public function setDateLastUnsubscribed(\DateTime $DateLastUnsubscribed)
    {
      $this->DateLastUnsubscribed = $DateLastUnsubscribed->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return string
     */
    public function getIPLastUnsubscribed()
    {
      return $this->IPLastUnsubscribed;
    }

    /**
     * @param string $IPLastUnsubscribed
     * @return SystemAttributes
     */
    public function setIPLastUnsubscribed($IPLastUnsubscribed)
    {
      $this->IPLastUnsubscribed = $IPLastUnsubscribed;
      return $this;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
      return $this->AccountId;
    }

    /**
     * @param int $AccountId
     * @return SystemAttributes
     */
    public function setAccountId($AccountId)
    {
      $this->AccountId = $AccountId;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsHBOnUpload()
    {
      return $this->IsHBOnUpload;
    }

    /**
     * @param boolean $IsHBOnUpload
     * @return SystemAttributes
     */
    public function setIsHBOnUpload($IsHBOnUpload)
    {
      $this->IsHBOnUpload = $IsHBOnUpload;
      return $this;
    }

    /**
     * @return string
     */
    public function getLastBounceReason()
    {
      return $this->LastBounceReason;
    }

    /**
     * @param string $LastBounceReason
     * @return SystemAttributes
     */
    public function setLastBounceReason($LastBounceReason)
    {
      $this->LastBounceReason = $LastBounceReason;
      return $this;
    }

    /**
     * @return string
     */
    public function getLastUnsubscribeMethod()
    {
      return $this->LastUnsubscribeMethod;
    }

    /**
     * @param string $LastUnsubscribeMethod
     * @return SystemAttributes
     */
    public function setLastUnsubscribeMethod($LastUnsubscribeMethod)
    {
      $this->LastUnsubscribeMethod = $LastUnsubscribeMethod;
      return $this;
    }

    /**
     * @return string
     */
    public function getUnsubscribePreviousStatus()
    {
      return $this->UnsubscribePreviousStatus;
    }

    /**
     * @param string $UnsubscribePreviousStatus
     * @return SystemAttributes
     */
    public function setUnsubscribePreviousStatus($UnsubscribePreviousStatus)
    {
      $this->UnsubscribePreviousStatus = $UnsubscribePreviousStatus;
      return $this;
    }

    /**
     * @return string
     */
    public function getOwnerEmail()
    {
      return $this->OwnerEmail;
    }

    /**
     * @param string $OwnerEmail
     * @return SystemAttributes
     */
    public function setOwnerEmail($OwnerEmail)
    {
      $this->OwnerEmail = $OwnerEmail;
      return $this;
    }

    /**
     * @return string
     */
    public function getOwnerFirstName()
    {
      return $this->OwnerFirstName;
    }

    /**
     * @param string $OwnerFirstName
     * @return SystemAttributes
     */
    public function setOwnerFirstName($OwnerFirstName)
    {
      $this->OwnerFirstName = $OwnerFirstName;
      return $this;
    }

    /**
     * @return string
     */
    public function getOwnerLastName()
    {
      return $this->OwnerLastName;
    }

    /**
     * @param string $OwnerLastName
     * @return SystemAttributes
     */
    public function setOwnerLastName($OwnerLastName)
    {
      $this->OwnerLastName = $OwnerLastName;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsLead()
    {
      return $this->isLead;
    }

    /**
     * @param boolean $isLead
     * @return SystemAttributes
     */
    public function setIsLead($isLead)
    {
      $this->isLead = $isLead;
      return $this;
    }

}
