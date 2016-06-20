<?php
namespace App\Library\Bronto;
class bounceObject
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
     * @var string $type
     */
    protected $type = null;

    /**
     * @var string $description
     */
    protected $description = null;

    /**
     * @var \DateTime $created
     */
    protected $created = null;

    /**
     * @param string $contactId
     * @param string $deliveryId
     * @param string $type
     * @param string $description
     * @param \DateTime $created
     */
    public function __construct($contactId, $deliveryId, $type, $description, \DateTime $created)
    {
      $this->contactId = $contactId;
      $this->deliveryId = $deliveryId;
      $this->type = $type;
      $this->description = $description;
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
     * @return bounceObject
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
     * @return bounceObject
     */
    public function setDeliveryId($deliveryId)
    {
      $this->deliveryId = $deliveryId;
      return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param string $type
     * @return bounceObject
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
      return $this->description;
    }

    /**
     * @param string $description
     * @return bounceObject
     */
    public function setDescription($description)
    {
      $this->description = $description;
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
     * @return bounceObject
     */
    public function setCreated(\DateTime $created)
    {
      $this->created = $created->format(\DateTime::ATOM);
      return $this;
    }

}
