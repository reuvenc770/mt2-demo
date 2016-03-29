<?php
namespace App\Library\Campaigner;
class ActivityResult
{

    /**
     * @var int $Opens
     */
    protected $Opens = null;

    /**
     * @var int $Clicks
     */
    protected $Clicks = null;

    /**
     * @var int $Replies
     */
    protected $Replies = null;

    /**
     * @var int $Unsubscribes
     */
    protected $Unsubscribes = null;

    /**
     * @var int $SpamComplaints
     */
    protected $SpamComplaints = null;

    /**
     * @param int $Opens
     * @param int $Clicks
     * @param int $Replies
     * @param int $Unsubscribes
     * @param int $SpamComplaints
     */
    public function __construct($Opens, $Clicks, $Replies, $Unsubscribes, $SpamComplaints)
    {
      $this->Opens = $Opens;
      $this->Clicks = $Clicks;
      $this->Replies = $Replies;
      $this->Unsubscribes = $Unsubscribes;
      $this->SpamComplaints = $SpamComplaints;
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
     * @return ActivityResult
     */
    public function setOpens($Opens)
    {
      $this->Opens = $Opens;
      return $this;
    }

    /**
     * @return int
     */
    public function getClicks()
    {
      return $this->Clicks;
    }

    /**
     * @param int $Clicks
     * @return ActivityResult
     */
    public function setClicks($Clicks)
    {
      $this->Clicks = $Clicks;
      return $this;
    }

    /**
     * @return int
     */
    public function getReplies()
    {
      return $this->Replies;
    }

    /**
     * @param int $Replies
     * @return ActivityResult
     */
    public function setReplies($Replies)
    {
      $this->Replies = $Replies;
      return $this;
    }

    /**
     * @return int
     */
    public function getUnsubscribes()
    {
      return $this->Unsubscribes;
    }

    /**
     * @param int $Unsubscribes
     * @return ActivityResult
     */
    public function setUnsubscribes($Unsubscribes)
    {
      $this->Unsubscribes = $Unsubscribes;
      return $this;
    }

    /**
     * @return int
     */
    public function getSpamComplaints()
    {
      return $this->SpamComplaints;
    }

    /**
     * @param int $SpamComplaints
     * @return ActivityResult
     */
    public function setSpamComplaints($SpamComplaints)
    {
      $this->SpamComplaints = $SpamComplaints;
      return $this;
    }

}
