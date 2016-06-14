<?php
namespace App\Library\Bronto;
class recentActivitySearchRequest
{

    /**
     * @var \DateTime $start
     */
    protected $start = null;

    /**
     * @var \DateTime $end
     */
    protected $end = null;

    /**
     * @var string $contactId
     */
    protected $contactId = null;

    /**
     * @var string $deliveryId
     */
    protected $deliveryId = null;

    /**
     * @var int $size
     */
    protected $size = null;

    /**
     * @var readDirection $readDirection
     */
    protected $readDirection = null;

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
      $this->start = $start->format(\DateTime::ATOM);
      $this->end = $end->format(\DateTime::ATOM);
      $this->contactId = $contactId;
      $this->deliveryId = $deliveryId;
      $this->size = $size;
      $this->readDirection = $readDirection;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
      if ($this->start == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->start);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $start
     * @return recentActivitySearchRequest
     */
    public function setStart(\DateTime $start)
    {
      $this->start = $start->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
      if ($this->end == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->end);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $end
     * @return recentActivitySearchRequest
     */
    public function setEnd(\DateTime $end)
    {
      $this->end = $end->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return string
     */
    public function getContactId()
    {
      return $this->contactId;
    }

    /**
     * @param string $contactId
     * @return recentActivitySearchRequest
     */
    public function setContactId($contactId)
    {
      $this->contactId = $contactId;
      return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryId()
    {
      return $this->deliveryId;
    }

    /**
     * @param string $deliveryId
     * @return recentActivitySearchRequest
     */
    public function setDeliveryId($deliveryId)
    {
      $this->deliveryId = $deliveryId;
      return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
      return $this->size;
    }

    /**
     * @param int $size
     * @return recentActivitySearchRequest
     */
    public function setSize($size)
    {
      $this->size = $size;
      return $this;
    }

    /**
     * @return readDirection
     */
    public function getReadDirection()
    {
      return $this->readDirection;
    }

    /**
     * @param readDirection $readDirection
     * @return recentActivitySearchRequest
     */
    public function setReadDirection($readDirection)
    {
      $this->readDirection = $readDirection;
      return $this;
    }

}
