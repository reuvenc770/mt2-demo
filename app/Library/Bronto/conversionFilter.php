<?php

class conversionFilter
{

    /**
     * @var string[] $contactId
     */
    protected $contactId = null;

    /**
     * @var string[] $deliveryId
     */
    protected $deliveryId = null;

    /**
     * @var string[] $id
     */
    protected $id = null;

    /**
     * @var string[] $orderId
     */
    protected $orderId = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string[]
     */
    public function getContactId()
    {
      return $this->contactId;
    }

    /**
     * @param string[] $contactId
     * @return conversionFilter
     */
    public function setContactId(array $contactId)
    {
      $this->contactId = $contactId;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getDeliveryId()
    {
      return $this->deliveryId;
    }

    /**
     * @param string[] $deliveryId
     * @return conversionFilter
     */
    public function setDeliveryId(array $deliveryId)
    {
      $this->deliveryId = $deliveryId;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string[] $id
     * @return conversionFilter
     */
    public function setId(array $id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getOrderId()
    {
      return $this->orderId;
    }

    /**
     * @param string[] $orderId
     * @return conversionFilter
     */
    public function setOrderId(array $orderId)
    {
      $this->orderId = $orderId;
      return $this;
    }

}
