<?php
namespace App\Library\Bronto;
class sessionHeader
{

    /**
     * @var string $sessionId
     */
    protected $sessionId = null;

    /**
     * @var boolean $disableHistory
     */
    protected $disableHistory = null;

    /**
     * @param string $sessionId
     * @param boolean $disableHistory
     */
    public function __construct($sessionId, $disableHistory)
    {
      $this->sessionId = $sessionId;
      $this->disableHistory = $disableHistory;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
      return $this->sessionId;
    }

    /**
     * @param string $sessionId
     * @return sessionHeader
     */
    public function setSessionId($sessionId)
    {
      $this->sessionId = $sessionId;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDisableHistory()
    {
      return $this->disableHistory;
    }

    /**
     * @param boolean $disableHistory
     * @return sessionHeader
     */
    public function setDisableHistory($disableHistory)
    {
      $this->disableHistory = $disableHistory;
      return $this;
    }

}
