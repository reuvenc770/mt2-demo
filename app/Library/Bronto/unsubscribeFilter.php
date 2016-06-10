<?php
namespace App\Library\Bronto;
class unsubscribeFilter
{

    /**
     * @var string $contactId
     */
    protected $contactId = null;

    /**
     * @var string $deliveryId
     */
    protected $deliveryId = null;

    /**
     * @var \DateTime $start
     */
    protected $start = null;

    /**
     * @var \DateTime $end
     */
    protected $end = null;

    /**
     * @param string $contactId
     * @param string $deliveryId
     * @param \DateTime $start
     * @param \DateTime $end
     */
    public function __construct($contactId, $deliveryId, \DateTime $start, \DateTime $end)
    {
      $this->contactId = $contactId;
      $this->deliveryId = $deliveryId;
      $this->start = $start->format(\DateTime::ATOM);
      $this->end = $end->format(\DateTime::ATOM);
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
     * @return unsubscribeFilter
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
     * @return unsubscribeFilter
     */
    public function setDeliveryId($deliveryId)
    {
      $this->deliveryId = $deliveryId;
      return $this;
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
     * @return unsubscribeFilter
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
     * @return unsubscribeFilter
     */
    public function setEnd(\DateTime $end)
    {
      $this->end = $end->format(\DateTime::ATOM);
      return $this;
    }

}
