<?php
namespace App\Library\Bronto;
class remailObject
{

    /**
     * @var int $days
     */
    protected $days = null;

    /**
     * @var string $time
     */
    protected $time = null;

    /**
     * @var string $subject
     */
    protected $subject = null;

    /**
     * @var string $messageId
     */
    protected $messageId = null;

    /**
     * @var string $activity
     */
    protected $activity = null;

    /**
     * @param int $days
     * @param string $time
     * @param string $subject
     * @param string $messageId
     * @param string $activity
     */
    public function __construct($days, $time, $subject, $messageId, $activity)
    {
      $this->days = $days;
      $this->time = $time;
      $this->subject = $subject;
      $this->messageId = $messageId;
      $this->activity = $activity;
    }

    /**
     * @return int
     */
    public function getDays()
    {
      return $this->days;
    }

    /**
     * @param int $days
     * @return remailObject
     */
    public function setDays($days)
    {
      $this->days = $days;
      return $this;
    }

    /**
     * @return string
     */
    public function getTime()
    {
      return $this->time;
    }

    /**
     * @param string $time
     * @return remailObject
     */
    public function setTime($time)
    {
      $this->time = $time;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
      return $this->subject;
    }

    /**
     * @param string $subject
     * @return remailObject
     */
    public function setSubject($subject)
    {
      $this->subject = $subject;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageId()
    {
      return $this->messageId;
    }

    /**
     * @param string $messageId
     * @return remailObject
     */
    public function setMessageId($messageId)
    {
      $this->messageId = $messageId;
      return $this;
    }

    /**
     * @return string
     */
    public function getActivity()
    {
      return $this->activity;
    }

    /**
     * @param string $activity
     * @return remailObject
     */
    public function setActivity($activity)
    {
      $this->activity = $activity;
      return $this;
    }

}
