<?php
namespace App\Library\Bronto;
class smsDeliveryFilter
{

    /**
     * @var filterType $type
     */
    protected $type = null;

    /**
     * @var string[] $id
     */
    protected $id = null;

    /**
     * @var string[] $messageId
     */
    protected $messageId = null;

    /**
     * @var dateValue[] $start
     */
    protected $start = null;

    /**
     * @var string[] $status
     */
    protected $status = null;

    /**
     * @var string[] $deliveryType
     */
    protected $deliveryType = null;

    /**
     * @param filterType $type
     */
    public function __construct($type)
    {
      $this->type = $type;
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
     * @return smsDeliveryFilter
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string[] $id
     * @return smsDeliveryFilter
     */
    public function setId(array $id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getMessageId()
    {
      return $this->messageId;
    }

    /**
     * @param string[] $messageId
     * @return smsDeliveryFilter
     */
    public function setMessageId(array $messageId)
    {
      $this->messageId = $messageId;
      return $this;
    }

    /**
     * @return dateValue[]
     */
    public function getStart()
    {
      return $this->start;
    }

    /**
     * @param dateValue[] $start
     * @return smsDeliveryFilter
     */
    public function setStart(array $start)
    {
      $this->start = $start;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getStatus()
    {
      return $this->status;
    }

    /**
     * @param string[] $status
     * @return smsDeliveryFilter
     */
    public function setStatus(array $status)
    {
      $this->status = $status;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getDeliveryType()
    {
      return $this->deliveryType;
    }

    /**
     * @param string[] $deliveryType
     * @return smsDeliveryFilter
     */
    public function setDeliveryType(array $deliveryType)
    {
      $this->deliveryType = $deliveryType;
      return $this;
    }

}
