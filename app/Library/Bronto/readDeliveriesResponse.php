<?php
namespace App\Library\Bronto;
class readDeliveriesResponse
{

    /**
     * @var deliveryObject[] $return
     */
    protected $return = null;

    /**
     * @param deliveryObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return deliveryObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param deliveryObject[] $return
     * @return readDeliveriesResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
