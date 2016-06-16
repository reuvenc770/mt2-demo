<?php
namespace App\Library\Bronto;
class activityObject
{

    /**
     * @var \DateTime $activityDate
     */
    protected $activityDate = null;

    /**
     * @var string $contactId
     */
    protected $contactId = null;

    /**
     * @var string $deliveryId
     */
    protected $deliveryId = null;

    /**
     * @var string $messageId
     */
    protected $messageId = null;

    /**
     * @var string $listId
     */
    protected $listId = null;

    /**
     * @var string $segmentId
     */
    protected $segmentId = null;

    /**
     * @var string $trackingType
     */
    protected $trackingType = null;

    /**
     * @var string $bounceReason
     */
    protected $bounceReason = null;

    /**
     * @var string $bounceType
     */
    protected $bounceType = null;

    /**
     * @var string $linkName
     */
    protected $linkName = null;

    /**
     * @var string $linkUrl
     */
    protected $linkUrl = null;

    /**
     * @var string $url
     */
    protected $url = null;

    /**
     * @var int $quantity
     */
    protected $quantity = null;

    /**
     * @var string $amount
     */
    protected $amount = null;

    /**
     * @param \DateTime $activityDate
     * @param string $contactId
     * @param string $deliveryId
     * @param string $messageId
     * @param string $listId
     * @param string $segmentId
     * @param string $trackingType
     * @param string $bounceReason
     * @param string $bounceType
     * @param string $linkName
     * @param string $linkUrl
     * @param string $url
     * @param int $quantity
     * @param string $amount
     */
    public function __construct(\DateTime $activityDate, $contactId, $deliveryId, $messageId, $listId, $segmentId, $trackingType, $bounceReason, $bounceType, $linkName, $linkUrl, $url, $quantity, $amount)
    {
      $this->activityDate = $activityDate->format(\DateTime::ATOM);
      $this->contactId = $contactId;
      $this->deliveryId = $deliveryId;
      $this->messageId = $messageId;
      $this->listId = $listId;
      $this->segmentId = $segmentId;
      $this->trackingType = $trackingType;
      $this->bounceReason = $bounceReason;
      $this->bounceType = $bounceType;
      $this->linkName = $linkName;
      $this->linkUrl = $linkUrl;
      $this->url = $url;
      $this->quantity = $quantity;
      $this->amount = $amount;
    }

    /**
     * @return \DateTime
     */
    public function getActivityDate()
    {
      if ($this->activityDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->activityDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $activityDate
     * @return activityObject
     */
    public function setActivityDate(\DateTime $activityDate)
    {
      $this->activityDate = $activityDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return string
     */
    public function getContactId()
    {
      return $this->contactId;
    }

    /**
     * @param string $contactId
     * @return activityObject
     */
    public function setContactId($contactId)
    {
      $this->contactId = $contactId;
      return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryId()
    {
      return $this->deliveryId;
    }

    /**
     * @param string $deliveryId
     * @return activityObject
     */
    public function setDeliveryId($deliveryId)
    {
      $this->deliveryId = $deliveryId;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageId()
    {
      return $this->messageId;
    }

    /**
     * @param string $messageId
     * @return activityObject
     */
    public function setMessageId($messageId)
    {
      $this->messageId = $messageId;
      return $this;
    }

    /**
     * @return string
     */
    public function getListId()
    {
      return $this->listId;
    }

    /**
     * @param string $listId
     * @return activityObject
     */
    public function setListId($listId)
    {
      $this->listId = $listId;
      return $this;
    }

    /**
     * @return string
     */
    public function getSegmentId()
    {
      return $this->segmentId;
    }

    /**
     * @param string $segmentId
     * @return activityObject
     */
    public function setSegmentId($segmentId)
    {
      $this->segmentId = $segmentId;
      return $this;
    }

    /**
     * @return string
     */
    public function getTrackingType()
    {
      return $this->trackingType;
    }

    /**
     * @param string $trackingType
     * @return activityObject
     */
    public function setTrackingType($trackingType)
    {
      $this->trackingType = $trackingType;
      return $this;
    }

    /**
     * @return string
     */
    public function getBounceReason()
    {
      return $this->bounceReason;
    }

    /**
     * @param string $bounceReason
     * @return activityObject
     */
    public function setBounceReason($bounceReason)
    {
      $this->bounceReason = $bounceReason;
      return $this;
    }

    /**
     * @return string
     */
    public function getBounceType()
    {
      return $this->bounceType;
    }

    /**
     * @param string $bounceType
     * @return activityObject
     */
    public function setBounceType($bounceType)
    {
      $this->bounceType = $bounceType;
      return $this;
    }

    /**
     * @return string
     */
    public function getLinkName()
    {
      return $this->linkName;
    }

    /**
     * @param string $linkName
     * @return activityObject
     */
    public function setLinkName($linkName)
    {
      $this->linkName = $linkName;
      return $this;
    }

    /**
     * @return string
     */
    public function getLinkUrl()
    {
      return $this->linkUrl;
    }

    /**
     * @param string $linkUrl
     * @return activityObject
     */
    public function setLinkUrl($linkUrl)
    {
      $this->linkUrl = $linkUrl;
      return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
      return $this->url;
    }

    /**
     * @param string $url
     * @return activityObject
     */
    public function setUrl($url)
    {
      $this->url = $url;
      return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
      return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return activityObject
     */
    public function setQuantity($quantity)
    {
      $this->quantity = $quantity;
      return $this;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
      return $this->amount;
    }

    /**
     * @param string $amount
     * @return activityObject
     */
    public function setAmount($amount)
    {
      $this->amount = $amount;
      return $this;
    }

}
