<?php
namespace App\Library\Bronto;
class addDeliveryGroup
{

    /**
     * @var deliveryGroupObject[] $deliveryGroups
     */
    protected $deliveryGroups = null;

    /**
     * @param deliveryGroupObject[] $deliveryGroups
     */
    public function __construct(array $deliveryGroups)
    {
      $this->deliveryGroups = $deliveryGroups;
    }

    /**
     * @return deliveryGroupObject[]
     */
    public function getDeliveryGroups()
    {
      return $this->deliveryGroups;
    }

    /**
     * @param deliveryGroupObject[] $deliveryGroups
     * @return addDeliveryGroup
     */
    public function setDeliveryGroups(array $deliveryGroups)
    {
      $this->deliveryGroups = $deliveryGroups;
      return $this;
    }

}
