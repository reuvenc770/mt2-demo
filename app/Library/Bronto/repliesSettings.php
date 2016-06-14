<?php
namespace App\Library\Bronto;
class repliesSettings
{

    /**
     * @var boolean $deletedAutomatedReplies
     */
    protected $deletedAutomatedReplies = null;

    /**
     * @var boolean $deleteSpam
     */
    protected $deleteSpam = null;

    /**
     * @var boolean $deleteUnsubscribeReplies
     */
    protected $deleteUnsubscribeReplies = null;

    /**
     * @var boolean $handleUnsubscribes
     */
    protected $handleUnsubscribes = null;

    /**
     * @var string $unsubscribeKeywords
     */
    protected $unsubscribeKeywords = null;

    /**
     * @var string $replyForwardEmail
     */
    protected $replyForwardEmail = null;

    /**
     * @param boolean $deletedAutomatedReplies
     * @param boolean $deleteSpam
     * @param boolean $deleteUnsubscribeReplies
     * @param boolean $handleUnsubscribes
     * @param string $unsubscribeKeywords
     * @param string $replyForwardEmail
     */
    public function __construct($deletedAutomatedReplies, $deleteSpam, $deleteUnsubscribeReplies, $handleUnsubscribes, $unsubscribeKeywords, $replyForwardEmail)
    {
      $this->deletedAutomatedReplies = $deletedAutomatedReplies;
      $this->deleteSpam = $deleteSpam;
      $this->deleteUnsubscribeReplies = $deleteUnsubscribeReplies;
      $this->handleUnsubscribes = $handleUnsubscribes;
      $this->unsubscribeKeywords = $unsubscribeKeywords;
      $this->replyForwardEmail = $replyForwardEmail;
    }

    /**
     * @return boolean
     */
    public function getDeletedAutomatedReplies()
    {
      return $this->deletedAutomatedReplies;
    }

    /**
     * @param boolean $deletedAutomatedReplies
     * @return repliesSettings
     */
    public function setDeletedAutomatedReplies($deletedAutomatedReplies)
    {
      $this->deletedAutomatedReplies = $deletedAutomatedReplies;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDeleteSpam()
    {
      return $this->deleteSpam;
    }

    /**
     * @param boolean $deleteSpam
     * @return repliesSettings
     */
    public function setDeleteSpam($deleteSpam)
    {
      $this->deleteSpam = $deleteSpam;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDeleteUnsubscribeReplies()
    {
      return $this->deleteUnsubscribeReplies;
    }

    /**
     * @param boolean $deleteUnsubscribeReplies
     * @return repliesSettings
     */
    public function setDeleteUnsubscribeReplies($deleteUnsubscribeReplies)
    {
      $this->deleteUnsubscribeReplies = $deleteUnsubscribeReplies;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getHandleUnsubscribes()
    {
      return $this->handleUnsubscribes;
    }

    /**
     * @param boolean $handleUnsubscribes
     * @return repliesSettings
     */
    public function setHandleUnsubscribes($handleUnsubscribes)
    {
      $this->handleUnsubscribes = $handleUnsubscribes;
      return $this;
    }

    /**
     * @return string
     */
    public function getUnsubscribeKeywords()
    {
      return $this->unsubscribeKeywords;
    }

    /**
     * @param string $unsubscribeKeywords
     * @return repliesSettings
     */
    public function setUnsubscribeKeywords($unsubscribeKeywords)
    {
      $this->unsubscribeKeywords = $unsubscribeKeywords;
      return $this;
    }

    /**
     * @return string
     */
    public function getReplyForwardEmail()
    {
      return $this->replyForwardEmail;
    }

    /**
     * @param string $replyForwardEmail
     * @return repliesSettings
     */
    public function setReplyForwardEmail($replyForwardEmail)
    {
      $this->replyForwardEmail = $replyForwardEmail;
      return $this;
    }

}
