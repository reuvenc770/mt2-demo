<?php

namespace App\Library\NetAtlantic;

class TrackingSummaryStruct
{

    /**
     * @var int $TransientFailure
     */
    protected $TransientFailure = null;

    /**
     * @var int $Success
     */
    protected $Success = null;

    /**
     * @var int $Expired
     */
    protected $Expired = null;

    /**
     * @var int $Paused
     */
    protected $Paused = null;

    /**
     * @var int $MailMergeSkipped
     */
    protected $MailMergeSkipped = null;

    /**
     * @var int $Active
     */
    protected $Active = null;

    /**
     * @var int $Opens
     */
    protected $Opens = null;

    /**
     * @var \DateTime $Created
     */
    protected $Created = null;

    /**
     * @var int $NotAttempted
     */
    protected $NotAttempted = null;

    /**
     * @var int $Clickthroughs
     */
    protected $Clickthroughs = null;

    /**
     * @var string $Title
     */
    protected $Title = null;

    /**
     * @var int $TotalRecipients
     */
    protected $TotalRecipients = null;

    /**
     * @var int $PermanentFailure
     */
    protected $PermanentFailure = null;

    /**
     * @var int $TotalUndelivered
     */
    protected $TotalUndelivered = null;

    /**
     * @var int $MailMergeAbort
     */
    protected $MailMergeAbort = null;

    /**
     * @var int $UniqueOpens
     */
    protected $UniqueOpens = null;

    /**
     * @var int $Clickstreams
     */
    protected $Clickstreams = null;

    /**
     * @var int $Pending
     */
    protected $Pending = null;

    /**
     * @var ArrayOfUrlTrackingStruct $Urls
     */
    protected $Urls = null;

    /**
     * @var int $MailingID
     */
    protected $MailingID = null;

    /**
     * @var int $Retry
     */
    protected $Retry = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return int
     */
    public function getTransientFailure()
    {
      return $this->TransientFailure;
    }

    /**
     * @param int $TransientFailure
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setTransientFailure($TransientFailure)
    {
      $this->TransientFailure = $TransientFailure;
      return $this;
    }

    /**
     * @return int
     */
    public function getSuccess()
    {
      return $this->Success;
    }

    /**
     * @param int $Success
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setSuccess($Success)
    {
      $this->Success = $Success;
      return $this;
    }

    /**
     * @return int
     */
    public function getExpired()
    {
      return $this->Expired;
    }

    /**
     * @param int $Expired
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setExpired($Expired)
    {
      $this->Expired = $Expired;
      return $this;
    }

    /**
     * @return int
     */
    public function getPaused()
    {
      return $this->Paused;
    }

    /**
     * @param int $Paused
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setPaused($Paused)
    {
      $this->Paused = $Paused;
      return $this;
    }

    /**
     * @return int
     */
    public function getMailMergeSkipped()
    {
      return $this->MailMergeSkipped;
    }

    /**
     * @param int $MailMergeSkipped
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setMailMergeSkipped($MailMergeSkipped)
    {
      $this->MailMergeSkipped = $MailMergeSkipped;
      return $this;
    }

    /**
     * @return int
     */
    public function getActive()
    {
      return $this->Active;
    }

    /**
     * @param int $Active
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setActive($Active)
    {
      $this->Active = $Active;
      return $this;
    }

    /**
     * @return int
     */
    public function getOpens()
    {
      return $this->Opens;
    }

    /**
     * @param int $Opens
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setOpens($Opens)
    {
      $this->Opens = $Opens;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
      if ($this->Created == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->Created);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $Created
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setCreated(\DateTime $Created = null)
    {
      if ($Created == null) {
       $this->Created = null;
      } else {
        $this->Created = $Created->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return int
     */
    public function getNotAttempted()
    {
      return $this->NotAttempted;
    }

    /**
     * @param int $NotAttempted
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setNotAttempted($NotAttempted)
    {
      $this->NotAttempted = $NotAttempted;
      return $this;
    }

    /**
     * @return int
     */
    public function getClickthroughs()
    {
      return $this->Clickthroughs;
    }

    /**
     * @param int $Clickthroughs
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setClickthroughs($Clickthroughs)
    {
      $this->Clickthroughs = $Clickthroughs;
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
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setTitle($Title)
    {
      $this->Title = $Title;
      return $this;
    }

    /**
     * @return int
     */
    public function getTotalRecipients()
    {
      return $this->TotalRecipients;
    }

    /**
     * @param int $TotalRecipients
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setTotalRecipients($TotalRecipients)
    {
      $this->TotalRecipients = $TotalRecipients;
      return $this;
    }

    /**
     * @return int
     */
    public function getPermanentFailure()
    {
      return $this->PermanentFailure;
    }

    /**
     * @param int $PermanentFailure
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setPermanentFailure($PermanentFailure)
    {
      $this->PermanentFailure = $PermanentFailure;
      return $this;
    }

    /**
     * @return int
     */
    public function getTotalUndelivered()
    {
      return $this->TotalUndelivered;
    }

    /**
     * @param int $TotalUndelivered
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setTotalUndelivered($TotalUndelivered)
    {
      $this->TotalUndelivered = $TotalUndelivered;
      return $this;
    }

    /**
     * @return int
     */
    public function getMailMergeAbort()
    {
      return $this->MailMergeAbort;
    }

    /**
     * @param int $MailMergeAbort
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setMailMergeAbort($MailMergeAbort)
    {
      $this->MailMergeAbort = $MailMergeAbort;
      return $this;
    }

    /**
     * @return int
     */
    public function getUniqueOpens()
    {
      return $this->UniqueOpens;
    }

    /**
     * @param int $UniqueOpens
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setUniqueOpens($UniqueOpens)
    {
      $this->UniqueOpens = $UniqueOpens;
      return $this;
    }

    /**
     * @return int
     */
    public function getClickstreams()
    {
      return $this->Clickstreams;
    }

    /**
     * @param int $Clickstreams
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setClickstreams($Clickstreams)
    {
      $this->Clickstreams = $Clickstreams;
      return $this;
    }

    /**
     * @return int
     */
    public function getPending()
    {
      return $this->Pending;
    }

    /**
     * @param int $Pending
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setPending($Pending)
    {
      $this->Pending = $Pending;
      return $this;
    }

    /**
     * @return ArrayOfUrlTrackingStruct
     */
    public function getUrls()
    {
      return $this->Urls;
    }

    /**
     * @param ArrayOfUrlTrackingStruct $Urls
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setUrls($Urls)
    {
      $this->Urls = $Urls;
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
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setMailingID($MailingID)
    {
      $this->MailingID = $MailingID;
      return $this;
    }

    /**
     * @return int
     */
    public function getRetry()
    {
      return $this->Retry;
    }

    /**
     * @param int $Retry
     * @return \App\Library\NetAtlantic\TrackingSummaryStruct
     */
    public function setRetry($Retry)
    {
      $this->Retry = $Retry;
      return $this;
    }

}
