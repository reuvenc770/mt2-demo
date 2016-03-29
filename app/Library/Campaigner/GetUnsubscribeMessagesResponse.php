<?php
namespace App\Library\Campaigner;
class GetUnsubscribeMessagesResponse
{

    /**
     * @var ArrayOfUnsubscribeMessageData $GetUnsubscribeMessagesResult
     */
    protected $GetUnsubscribeMessagesResult = null;

    /**
     * @param ArrayOfUnsubscribeMessageData $GetUnsubscribeMessagesResult
     */
    public function __construct($GetUnsubscribeMessagesResult)
    {
      $this->GetUnsubscribeMessagesResult = $GetUnsubscribeMessagesResult;
    }

    /**
     * @return ArrayOfUnsubscribeMessageData
     */
    public function getGetUnsubscribeMessagesResult()
    {
      return $this->GetUnsubscribeMessagesResult;
    }

    /**
     * @param ArrayOfUnsubscribeMessageData $GetUnsubscribeMessagesResult
     * @return GetUnsubscribeMessagesResponse
     */
    public function setGetUnsubscribeMessagesResult($GetUnsubscribeMessagesResult)
    {
      $this->GetUnsubscribeMessagesResult = $GetUnsubscribeMessagesResult;
      return $this;
    }

}
