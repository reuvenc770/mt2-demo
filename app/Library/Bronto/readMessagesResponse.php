<?php
namespace App\Library\Bronto;
class readMessagesResponse
{

    /**
     * @var messageObject[] $return
     */
    protected $return = null;

    /**
     * @param messageObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return messageObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param messageObject[] $return
     * @return readMessagesResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
