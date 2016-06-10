<?php
namespace App\Library\Bronto;
class updateSMSMessages
{

    /**
     * @var smsMessageObject[] $messages
     */
    protected $messages = null;

    /**
     * @param smsMessageObject[] $messages
     */
    public function __construct(array $messages)
    {
      $this->messages = $messages;
    }

    /**
     * @return smsMessageObject[]
     */
    public function getMessages()
    {
      return $this->messages;
    }

    /**
     * @param smsMessageObject[] $messages
     * @return updateSMSMessages
     */
    public function setMessages(array $messages)
    {
      $this->messages = $messages;
      return $this;
    }

}
