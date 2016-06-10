<?php
namespace App\Library\Bronto;
class updateDeliveryGroup
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
     * @return updateDeliveryGroup
     */
    public function setDeliveryGroups(array $deliveryGroups)
    {
      $this->deliveryGroups = $deliveryGroups;
      return $this;
    }

}
