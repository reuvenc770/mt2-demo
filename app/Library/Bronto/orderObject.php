<?php
namespace App\Library\Bronto;
class orderObject
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var string $contactId
     */
    protected $contactId = null;

    /**
     * @var string $email
     */
    protected $email = null;

    /**
     * @var productObject[] $products
     */
    protected $products = null;

    /**
     * @var \DateTime $orderDate
     */
    protected $orderDate = null;

    /**
     * @var string $deliveryId
     */
    protected $deliveryId = null;

    /**
     * @var string $messageId
     */
    protected $messageId = null;

    /**
     * @var string $automatorId
     */
    protected $automatorId = null;

    /**
     * @var string $listId
     */
    protected $listId = null;

    /**
     * @var string $segmentId
     */
    protected $segmentId = null;

    /**
     * @var string $deliveryType
     */
    protected $deliveryType = null;

    /**
     * @var string $tid
     */
    protected $tid = null;

    /**
     * @param string $id
     * @param string $contactId
     * @param string $email
     * @param \DateTime $orderDate
     * @param string $deliveryId
     * @param string $messageId
     * @param string $automatorId
     * @param string $listId
     * @param string $segmentId
     * @param string $deliveryType
     * @param string $tid
     */
    public function __construct($id, $contactId, $email, \DateTime $orderDate, $deliveryId, $messageId, $automatorId, $listId, $segmentId, $deliveryType, $tid)
    {
      $this->id = $id;
      $this->contactId = $contactId;
      $this->email = $email;
      $this->orderDate = $orderDate->format(\DateTime::ATOM);
      $this->deliveryId = $deliveryId;
      $this->messageId = $messageId;
      $this->automatorId = $automatorId;
      $this->listId = $listId;
      $this->segmentId = $segmentId;
      $this->deliveryType = $deliveryType;
      $this->tid = $tid;
    }

    /**
     * @return string
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string $id
     * @return orderObject
     */
    public function setId($id)
    {
      $this->id = $id;
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
     * @return orderObject
     */
    public function setContactId($contactId)
    {
      $this->contactId = $contactId;
      return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
      return $this->email;
    }

    /**
     * @param string $email
     * @return orderObject
     */
    public function setEmail($email)
    {
      $this->email = $email;
      return $this;
    }

    /**
     * @return productObject[]
     */
    public function getProducts()
    {
      return $this->products;
    }

    /**
     * @param productObject[] $products
     * @return orderObject
     */
    public function setProducts(array $products)
    {
      $this->products = $products;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getOrderDate()
    {
      if ($this->orderDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->orderDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $orderDate
     * @return orderObject
     */
    public function setOrderDate(\DateTime $orderDate)
    {
      $this->orderDate = $orderDate->format(\DateTime::ATOM);
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
     * @return orderObject
     */
    public function setDeliveryId($deliveryId)
    {
      $this->deliveryId = $deliveryId;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageId()
    {
      return $this->messageId;
    }

    /**
     * @param string $messageId
     * @return orderObject
     */
    public function setMessageId($messageId)
    {
      $this->messageId = $messageId;
      return $this;
    }

    /**
     * @return string
     */
    public function getAutomatorId()
    {
      return $this->automatorId;
    }

    /**
     * @param string $automatorId
     * @return orderObject
     */
    public function setAutomatorId($automatorId)
    {
      $this->automatorId = $automatorId;
      return $this;
    }

    /**
     * @return string
     */
    public function getListId()
    {
      return $this->listId;
    }

    /**
     * @param string $listId
     * @return orderObject
     */
    public function setListId($listId)
    {
      $this->listId = $listId;
      return $this;
    }

    /**
     * @return string
     */
    public function getSegmentId()
    {
      return $this->segmentId;
    }

    /**
     * @param string $segmentId
     * @return orderObject
     */
    public function setSegmentId($segmentId)
    {
      $this->segmentId = $segmentId;
      return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryType()
    {
      return $this->deliveryType;
    }

    /**
     * @param string $deliveryType
     * @return orderObject
     */
    public function setDeliveryType($deliveryType)
    {
      $this->deliveryType = $deliveryType;
      return $this;
    }

    /**
     * @return string
     */
    public function getTid()
    {
      return $this->tid;
    }

    /**
     * @param string $tid
     * @return orderObject
     */
    public function setTid($tid)
    {
      $this->tid = $tid;
      return $this;
    }

}
