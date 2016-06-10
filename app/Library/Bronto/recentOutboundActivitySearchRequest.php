<?php
namespace App\Library\Bronto;
class recentOutboundActivitySearchRequest extends recentActivitySearchRequest
{

    /**
     * @var string[] $types
     */
    protected $types = null;

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @param string $contactId
     * @param string $deliveryId
     * @param int $size
     * @param readDirection $readDirection
     */
    public function __construct(\DateTime $start, \DateTime $end, $contactId, $deliveryId, $size, $readDirection)
    {
      parent::__construct($start, $end, $contactId, $deliveryId, $size, $readDirection);
    }

    /**
     * @return string[]
     */
    public function getTypes()
    {
      return $this->types;
    }

    /**
     * @param string[] $types
     * @return recentOutboundActivitySearchRequest
     */
    public function setTypes(array $types)
    {
      $this->types = $types;
      return $this;
    }

}
