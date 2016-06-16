<?php
namespace App\Library\Bronto;
class addToDeliveryGroup
{

    /**
     * @var deliveryGroupObject $deliveryGroup
     */
    protected $deliveryGroup = null;

    /**
     * @var string[] $deliveryIds
     */
    protected $deliveryIds = null;

    /**
     * @var string[] $messageIds
     */
    protected $messageIds = null;

    /**
     * @var string[] $messageRuleIds
     */
    protected $messageRuleIds = null;

    /**
     * @param deliveryGroupObject $deliveryGroup
     * @param string[] $deliveryIds
     * @param string[] $messageIds
     * @param string[] $messageRuleIds
     */
    public function __construct($deliveryGroup, array $deliveryIds, array $messageIds, array $messageRuleIds)
    {
      $this->deliveryGroup = $deliveryGroup;
      $this->deliveryIds = $deliveryIds;
      $this->messageIds = $messageIds;
      $this->messageRuleIds = $messageRuleIds;
    }

    /**
     * @return deliveryGroupObject
     */
    public function getDeliveryGroup()
    {
      return $this->deliveryGroup;
    }

    /**
     * @param deliveryGroupObject $deliveryGroup
     * @return addToDeliveryGroup
     */
    public function setDeliveryGroup($deliveryGroup)
    {
      $this->deliveryGroup = $deliveryGroup;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getDeliveryIds()
    {
      return $this->deliveryIds;
    }

    /**
     * @param string[] $deliveryIds
     * @return addToDeliveryGroup
     */
    public function setDeliveryIds(array $deliveryIds)
    {
      $this->deliveryIds = $deliveryIds;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getMessageIds()
    {
      return $this->messageIds;
    }

    /**
     * @param string[] $messageIds
     * @return addToDeliveryGroup
     */
    public function setMessageIds(array $messageIds)
    {
      $this->messageIds = $messageIds;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getMessageRuleIds()
    {
      return $this->messageRuleIds;
    }

    /**
     * @param string[] $messageRuleIds
     * @return addToDeliveryGroup
     */
    public function setMessageRuleIds(array $messageRuleIds)
    {
      $this->messageRuleIds = $messageRuleIds;
      return $this;
    }

}
