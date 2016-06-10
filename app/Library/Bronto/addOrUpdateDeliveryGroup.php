<?php
namespace App\Library\Bronto;
class addOrUpdateDeliveryGroup
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
     * @return addOrUpdateDeliveryGroup
     */
    public function setDeliveryGroups(array $deliveryGroups)
    {
      $this->deliveryGroups = $deliveryGroups;
      return $this;
    }

}
