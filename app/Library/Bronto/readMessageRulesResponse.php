<?php
namespace App\Library\Bronto;
class readMessageRulesResponse
{

    /**
     * @var messageRuleObject[] $return
     */
    protected $return = null;

    /**
     * @param messageRuleObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return messageRuleObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param messageRuleObject[] $return
     * @return readMessageRulesResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
