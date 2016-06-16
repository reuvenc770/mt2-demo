<?php

class deliveryGroupFilter
{

    /**
     * @var string[] $deliveryGroupId
     */
    protected $deliveryGroupId = null;

    /**
     * @var memberType $listByType
     */
    protected $listByType = null;

    /**
     * @var string[] $automatorId
     */
    protected $automatorId = null;

    /**
     * @var string[] $messageGroupId
     */
    protected $messageGroupId = null;

    /**
     * @var string[] $deliveryId
     */
    protected $deliveryId = null;

    /**
     * @var stringValue[] $name
     */
    protected $name = null;

    /**
     * @param memberType $listByType
     */
    public function __construct($listByType)
    {
      $this->listByType = $listByType;
    }

    /**
     * @return string[]
     */
    public function getDeliveryGroupId()
    {
      return $this->deliveryGroupId;
    }

    /**
     * @param string[] $deliveryGroupId
     * @return deliveryGroupFilter
     */
    public function setDeliveryGroupId(array $deliveryGroupId)
    {
      $this->deliveryGroupId = $deliveryGroupId;
      return $this;
    }

    /**
     * @return memberType
     */
    public function getListByType()
    {
      return $this->listByType;
    }

    /**
     * @param memberType $listByType
     * @return deliveryGroupFilter
     */
    public function setListByType($listByType)
    {
      $this->listByType = $listByType;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getAutomatorId()
    {
      return $this->automatorId;
    }

    /**
     * @param string[] $automatorId
     * @return deliveryGroupFilter
     */
    public function setAutomatorId(array $automatorId)
    {
      $this->automatorId = $automatorId;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getMessageGroupId()
    {
      return $this->messageGroupId;
    }

    /**
     * @param string[] $messageGroupId
     * @return deliveryGroupFilter
     */
    public function setMessageGroupId(array $messageGroupId)
    {
      $this->messageGroupId = $messageGroupId;
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
     * @return deliveryGroupFilter
     */
    public function setDeliveryId(array $deliveryId)
    {
      $this->deliveryId = $deliveryId;
      return $this;
    }

    /**
     * @return stringValue[]
     */
    public function getName()
    {
      return $this->name;
    }

    /**
     * @param stringValue[] $name
     * @return deliveryGroupFilter
     */
    public function setName(array $name)
    {
      $this->name = $name;
      return $this;
    }

}
