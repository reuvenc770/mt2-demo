<?php

class deleteMessageRules
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
     * @return deleteMessageRules
     */
    public function setMessageRules(array $messageRules)
    {
      $this->messageRules = $messageRules;
      return $this;
    }

}
