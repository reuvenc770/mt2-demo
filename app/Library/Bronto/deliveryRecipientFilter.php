<?php

class deliveryRecipientFilter
{

    /**
     * @var filterType $type
     */
    protected $type = null;

    /**
     * @var string $deliveryId
     */
    protected $deliveryId = null;

    /**
     * @var string[] $listIds
     */
    protected $listIds = null;

    /**
     * @var string[] $segmentIds
     */
    protected $segmentIds = null;

    /**
     * @var string[] $contactIds
     */
    protected $contactIds = null;

    /**
     * @param filterType $type
     * @param string $deliveryId
     */
    public function __construct($type, $deliveryId)
    {
      $this->type = $type;
      $this->deliveryId = $deliveryId;
    }

    /**
     * @return filterType
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param filterType $type
     * @return deliveryRecipientFilter
     */
    public function setType($type)
    {
      $this->type = $type;
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
     * @return deliveryRecipientFilter
     */
    public function setDeliveryId($deliveryId)
    {
      $this->deliveryId = $deliveryId;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getListIds()
    {
      return $this->listIds;
    }

    /**
     * @param string[] $listIds
     * @return deliveryRecipientFilter
     */
    public function setListIds(array $listIds)
    {
      $this->listIds = $listIds;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getSegmentIds()
    {
      return $this->segmentIds;
    }

    /**
     * @param string[] $segmentIds
     * @return deliveryRecipientFilter
     */
    public function setSegmentIds(array $segmentIds)
    {
      $this->segmentIds = $segmentIds;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getContactIds()
    {
      return $this->contactIds;
    }

    /**
     * @param string[] $contactIds
     * @return deliveryRecipientFilter
     */
    public function setContactIds(array $contactIds)
    {
      $this->contactIds = $contactIds;
      return $this;
    }

}
