<?php
namespace App\Library\Bronto;
class readSMSMessagesResponse
{

    /**
     * @var smsMessageObject[] $return
     */
    protected $return = null;

    /**
     * @param smsMessageObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return smsMessageObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param smsMessageObject[] $return
     * @return readSMSMessagesResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
