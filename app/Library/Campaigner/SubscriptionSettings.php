<?php
namespace App\Library\Campaigner;
class SubscriptionSettings
{

    /**
     * @var int $SmfGroupId
     */
    protected $SmfGroupId = null;

    /**
     * @var int $UnsubscribeFormId
     */
    protected $UnsubscribeFormId = null;

    /**
     * @var int $UnsubscribeMessageId
     */
    protected $UnsubscribeMessageId = null;

    /**
     * @param int $SmfGroupId
     * @param int $UnsubscribeFormId
     * @param int $UnsubscribeMessageId
     */
    public function __construct($SmfGroupId, $UnsubscribeFormId, $UnsubscribeMessageId)
    {
      $this->SmfGroupId = $SmfGroupId;
      $this->UnsubscribeFormId = $UnsubscribeFormId;
      $this->UnsubscribeMessageId = $UnsubscribeMessageId;
    }

    /**
     * @return int
     */
    public function getSmfGroupId()
    {
      return $this->SmfGroupId;
    }

    /**
     * @param int $SmfGroupId
     * @return SubscriptionSettings
     */
    public function setSmfGroupId($SmfGroupId)
    {
      $this->SmfGroupId = $SmfGroupId;
      return $this;
    }

    /**
     * @return int
     */
    public function getUnsubscribeFormId()
    {
      return $this->UnsubscribeFormId;
    }

    /**
     * @param int $UnsubscribeFormId
     * @return SubscriptionSettings
     */
    public function setUnsubscribeFormId($UnsubscribeFormId)
    {
      $this->UnsubscribeFormId = $UnsubscribeFormId;
      return $this;
    }

    /**
     * @return int
     */
    public function getUnsubscribeMessageId()
    {
      return $this->UnsubscribeMessageId;
    }

    /**
     * @param int $UnsubscribeMessageId
     * @return SubscriptionSettings
     */
    public function setUnsubscribeMessageId($UnsubscribeMessageId)
    {
      $this->UnsubscribeMessageId = $UnsubscribeMessageId;
      return $this;
    }

}
