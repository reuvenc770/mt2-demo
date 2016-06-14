<?php
namespace App\Library\Bronto;
class readSMSDeliveriesResponse
{

    /**
     * @var smsDeliveryObject[] $return
     */
    protected $return = null;

    /**
     * @param smsDeliveryObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return smsDeliveryObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param smsDeliveryObject[] $return
     * @return readSMSDeliveriesResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
