<?php
namespace App\Library\Bronto;
class readDeliveryRecipientsResponse
{

    /**
     * @var deliveryRecipientStatObject[] $return
     */
    protected $return = null;

    /**
     * @param deliveryRecipientStatObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return deliveryRecipientStatObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param deliveryRecipientStatObject[] $return
     * @return readDeliveryRecipientsResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
