<?php
namespace App\Library\Bronto;
class addSMSDeliveries
{

    /**
     * @var smsDeliveryObject[] $smsdeliveries
     */
    protected $smsdeliveries = null;

    /**
     * @param smsDeliveryObject[] $smsdeliveries
     */
    public function __construct(array $smsdeliveries)
    {
      $this->smsdeliveries = $smsdeliveries;
    }

    /**
     * @return smsDeliveryObject[]
     */
    public function getSmsdeliveries()
    {
      return $this->smsdeliveries;
    }

    /**
     * @param smsDeliveryObject[] $smsdeliveries
     * @return addSMSDeliveries
     */
    public function setSmsdeliveries(array $smsdeliveries)
    {
      $this->smsdeliveries = $smsdeliveries;
      return $this;
    }

}
