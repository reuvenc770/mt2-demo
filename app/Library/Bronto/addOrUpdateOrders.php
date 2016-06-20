<?php
namespace App\Library\Bronto;
class addOrUpdateOrders
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
     * @return addOrUpdateOrders
     */
    public function setOrders(array $orders)
    {
      $this->orders = $orders;
      return $this;
    }

}
