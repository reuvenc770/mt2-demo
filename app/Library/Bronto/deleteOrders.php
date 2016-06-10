<?php

class deleteOrders
{

    /**
     * @var orderObject[] $orders
     */
    protected $orders = null;

    /**
     * @param orderObject[] $orders
     */
    public function __construct(array $orders)
    {
      $this->orders = $orders;
    }

    /**
     * @return orderObject[]
     */
    public function getOrders()
    {
      return $this->orders;
    }

    /**
     * @param orderObject[] $orders
     * @return deleteOrders
     */
    public function setOrders(array $orders)
    {
      $this->orders = $orders;
      return $this;
    }

}
