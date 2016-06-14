<?php
namespace App\Library\Bronto;
class updateMessageRules
{

    /**
     * @var messageRuleObject[] $messageRules
     */
    protected $messageRules = null;

    /**
     * @param messageRuleObject[] $messageRules
     */
    public function __construct(array $messageRules)
    {
      $this->messageRules = $messageRules;
    }

    /**
     * @return messageRuleObject[]
     */
    public function getMessageRules()
    {
      return $this->messageRules;
    }

    /**
     * @param messageRuleObject[] $messageRules
     * @return updateMessageRules
     */
    public function setMessageRules(array $messageRules)
    {
      $this->messageRules = $messageRules;
      return $this;
    }

}
