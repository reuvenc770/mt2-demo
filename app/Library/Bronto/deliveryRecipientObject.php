<?php

class deliveryRecipientObject
{

    /**
     * @var string $deliveryType
     */
    protected $deliveryType = null;

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var string $type
     */
    protected $type = null;

    /**
     * @param string $deliveryType
     * @param string $id
     * @param string $type
     */
    public function __construct($deliveryType, $id, $type)
    {
      $this->deliveryType = $deliveryType;
      $this->id = $id;
      $this->type = $type;
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
     * @return deliveryRecipientObject
     */
    public function setDeliveryType($deliveryType)
    {
      $this->deliveryType = $deliveryType;
      return $this;
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
     * @return deliveryRecipientObject
     */
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param string $type
     * @return deliveryRecipientObject
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

}
