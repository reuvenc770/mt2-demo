<?php
namespace App\Library\Bronto;
class unsubscribeObject
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
     * @var string $method
     */
    protected $method = null;

    /**
     * @var string $complaint
     */
    protected $complaint = null;

    /**
     * @var \DateTime $created
     */
    protected $created = null;

    /**
     * @param string $contactId
     * @param string $deliveryId
     * @param string $method
     * @param string $complaint
     * @param \DateTime $created
     */
    public function __construct($contactId, $deliveryId, $method, $complaint, \DateTime $created)
    {
      $this->contactId = $contactId;
      $this->deliveryId = $deliveryId;
      $this->method = $method;
      $this->complaint = $complaint;
      $this->created = $created->format(\DateTime::ATOM);
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
     * @return unsubscribeObject
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
     * @return unsubscribeObject
     */
    public function setDeliveryId($deliveryId)
    {
      $this->deliveryId = $deliveryId;
      return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
      return $this->method;
    }

    /**
     * @param string $method
     * @return unsubscribeObject
     */
    public function setMethod($method)
    {
      $this->method = $method;
      return $this;
    }

    /**
     * @return string
     */
    public function getComplaint()
    {
      return $this->complaint;
    }

    /**
     * @param string $complaint
     * @return unsubscribeObject
     */
    public function setComplaint($complaint)
    {
      $this->complaint = $complaint;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
      if ($this->created == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->created);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $created
     * @return unsubscribeObject
     */
    public function setCreated(\DateTime $created)
    {
      $this->created = $created->format(\DateTime::ATOM);
      return $this;
    }

}
