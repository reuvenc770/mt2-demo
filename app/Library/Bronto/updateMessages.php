<?php
namespace App\Library\Bronto;
class updateMessages
{

    /**
     * @var messageObject[] $messages
     */
    protected $messages = null;

    /**
     * @param messageObject[] $messages
     */
    public function __construct(array $messages)
    {
      $this->messages = $messages;
    }

    /**
     * @return messageObject[]
     */
    public function getMessages()
    {
      return $this->messages;
    }

    /**
     * @param messageObject[] $messages
     * @return updateMessages
     */
    public function setMessages(array $messages)
    {
      $this->messages = $messages;
      return $this;
    }

}
