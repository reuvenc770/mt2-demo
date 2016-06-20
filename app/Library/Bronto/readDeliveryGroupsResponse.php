<?php
namespace App\Library\Bronto;
class readDeliveryGroupsResponse
{

    /**
     * @var deliveryGroupObject[] $return
     */
    protected $return = null;

    /**
     * @param deliveryGroupObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return deliveryGroupObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param deliveryGroupObject[] $return
     * @return readDeliveryGroupsResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
