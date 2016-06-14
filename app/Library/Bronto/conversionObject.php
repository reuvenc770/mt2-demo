<?php

class conversionObject
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var string $contactId
     */
    protected $contactId = null;

    /**
     * @var string $email
     */
    protected $email = null;

    /**
     * @var string $orderId
     */
    protected $orderId = null;

    /**
     * @var string $item
     */
    protected $item = null;

    /**
     * @var string $description
     */
    protected $description = null;

    /**
     * @var int $quantity
     */
    protected $quantity = null;

    /**
     * @var float $amount
     */
    protected $amount = null;

    /**
     * @var float $orderTotal
     */
    protected $orderTotal = null;

    /**
     * @var \DateTime $createdDate
     */
    protected $createdDate = null;

    /**
     * @var string $deliveryId
     */
    protected $deliveryId = null;

    /**
     * @var string $messageId
     */
    protected $messageId = null;

    /**
     * @var string $automatorId
     */
    protected $automatorId = null;

    /**
     * @var string $listId
     */
    protected $listId = null;

    /**
     * @var string $segmentId
     */
    protected $segmentId = null;

    /**
     * @var string $deliveryType
     */
    protected $deliveryType = null;

    /**
     * @var string $tid
     */
    protected $tid = null;

    /**
     * @param string $id
     * @param string $contactId
     * @param string $email
     * @param string $orderId
     * @param string $item
     * @param string $description
     * @param int $quantity
     * @param float $amount
     * @param float $orderTotal
     * @param \DateTime $createdDate
     * @param string $deliveryId
     * @param string $messageId
     * @param string $automatorId
     * @param string $listId
     * @param string $segmentId
     * @param string $deliveryType
     * @param string $tid
     */
    public function __construct($id, $contactId, $email, $orderId, $item, $description, $quantity, $amount, $orderTotal, \DateTime $createdDate, $deliveryId, $messageId, $automatorId, $listId, $segmentId, $deliveryType, $tid)
    {
      $this->id = $id;
      $this->contactId = $contactId;
      $this->email = $email;
      $this->orderId = $orderId;
      $this->item = $item;
      $this->description = $description;
      $this->quantity = $quantity;
      $this->amount = $amount;
      $this->orderTotal = $orderTotal;
      $this->createdDate = $createdDate->format(\DateTime::ATOM);
      $this->deliveryId = $deliveryId;
      $this->messageId = $messageId;
      $this->automatorId = $automatorId;
      $this->listId = $listId;
      $this->segmentId = $segmentId;
      $this->deliveryType = $deliveryType;
      $this->tid = $tid;
    }

    /**
     * @return string
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string $id
     * @return conversionObject
     */
    public function setId($id)
    {
      $this->id = $id;
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
     * @return conversionObject
     */
    public function setContactId($contactId)
    {
      $this->contactId = $contactId;
      return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
      return $this->email;
    }

    /**
     * @param string $email
     * @return conversionObject
     */
    public function setEmail($email)
    {
      $this->email = $email;
      return $this;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
      return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return conversionObject
     */
    public function setOrderId($orderId)
    {
      $this->orderId = $orderId;
      return $this;
    }

    /**
     * @return string
     */
    public function getItem()
    {
      return $this->item;
    }

    /**
     * @param string $item
     * @return conversionObject
     */
    public function setItem($item)
    {
      $this->item = $item;
      return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
      return $this->description;
    }

    /**
     * @param string $description
     * @return conversionObject
     */
    public function setDescription($description)
    {
      $this->description = $description;
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
     * @return conversionObject
     */
    public function setQuantity($quantity)
    {
      $this->quantity = $quantity;
      return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
      return $this->amount;
    }

    /**
     * @param float $amount
     * @return conversionObject
     */
    public function setAmount($amount)
    {
      $this->amount = $amount;
      return $this;
    }

    /**
     * @return float
     */
    public function getOrderTotal()
    {
      return $this->orderTotal;
    }

    /**
     * @param float $orderTotal
     * @return conversionObject
     */
    public function setOrderTotal($orderTotal)
    {
      $this->orderTotal = $orderTotal;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
      if ($this->createdDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->createdDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $createdDate
     * @return conversionObject
     */
    public function setCreatedDate(\DateTime $createdDate)
    {
      $this->createdDate = $createdDate->format(\DateTime::ATOM);
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
     * @return conversionObject
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
     * @return conversionObject
     */
    public function setMessageId($messageId)
    {
      $this->messageId = $messageId;
      return $this;
    }

    /**
     * @return string
     */
    public function getAutomatorId()
    {
      return $this->automatorId;
    }

    /**
     * @param string $automatorId
     * @return conversionObject
     */
    public function setAutomatorId($automatorId)
    {
      $this->automatorId = $automatorId;
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
     * @return conversionObject
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
     * @return conversionObject
     */
    public function setSegmentId($segmentId)
    {
      $this->segmentId = $segmentId;
      return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryType()
    {
      return $this->deliveryType;
    }

    /**
     * @param string $deliveryType
     * @return conversionObject
     */
    public function setDeliveryType($deliveryType)
    {
      $this->deliveryType = $deliveryType;
      return $this;
    }

    /**
     * @return string
     */
    public function getTid()
    {
      return $this->tid;
    }

    /**
     * @param string $tid
     * @return conversionObject
     */
    public function setTid($tid)
    {
      $this->tid = $tid;
      return $this;
    }

}
