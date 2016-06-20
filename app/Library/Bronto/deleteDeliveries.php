<?php

class deleteDeliveries
{

    /**
     * @var deliveryObject[] $deliveries
     */
    protected $deliveries = null;

    /**
     * @param deliveryObject[] $deliveries
     */
    public function __construct(array $deliveries)
    {
      $this->deliveries = $deliveries;
    }

    /**
     * @return deliveryObject[]
     */
    public function getDeliveries()
    {
      return $this->deliveries;
    }

    /**
     * @param deliveryObject[] $deliveries
     * @return deleteDeliveries
     */
    public function setDeliveries(array $deliveries)
    {
      $this->deliveries = $deliveries;
      return $this;
    }

}
