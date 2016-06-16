<?php
namespace App\Library\Bronto;
class smsDeliveryObject
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var \DateTime $start
     */
    protected $start = null;

    /**
     * @var string $messageId
     */
    protected $messageId = null;

    /**
     * @var string $deliveryType
     */
    protected $deliveryType = null;

    /**
     * @var string $status
     */
    protected $status = null;

    /**
     * @var string $content
     */
    protected $content = null;

    /**
     * @var deliveryRecipientObject[] $recipients
     */
    protected $recipients = null;

    /**
     * @var smsDeliveryContactsObject $contactRecipients
     */
    protected $contactRecipients = null;

    /**
     * @var string[] $keywords
     */
    protected $keywords = null;

    /**
     * @var smsMessageFieldObject[] $fields
     */
    protected $fields = null;

    /**
     * @var int $numSends
     */
    protected $numSends = null;

    /**
     * @var int $numDeliveries
     */
    protected $numDeliveries = null;

    /**
     * @var int $numIncoming
     */
    protected $numIncoming = null;

    /**
     * @var int $numBounces
     */
    protected $numBounces = null;

    /**
     * @var float $deliveryRate
     */
    protected $deliveryRate = null;

    /**
     * @param string $id
     * @param \DateTime $start
     * @param string $messageId
     * @param string $deliveryType
     * @param string $status
     * @param string $content
     * @param smsDeliveryContactsObject $contactRecipients
     * @param int $numSends
     * @param int $numDeliveries
     * @param int $numIncoming
     * @param int $numBounces
     * @param float $deliveryRate
     */
    public function __construct($id, \DateTime $start, $messageId, $deliveryType, $status, $content, $contactRecipients, $numSends, $numDeliveries, $numIncoming, $numBounces, $deliveryRate)
    {
      $this->id = $id;
      $this->start = $start->format(\DateTime::ATOM);
      $this->messageId = $messageId;
      $this->deliveryType = $deliveryType;
      $this->status = $status;
      $this->content = $content;
      $this->contactRecipients = $contactRecipients;
      $this->numSends = $numSends;
      $this->numDeliveries = $numDeliveries;
      $this->numIncoming = $numIncoming;
      $this->numBounces = $numBounces;
      $this->deliveryRate = $deliveryRate;
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
     * @return smsDeliveryObject
     */
    public function setId($id)
    {
      $this->id = $id;
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
     * @return smsDeliveryObject
     */
    public function setStart(\DateTime $start)
    {
      $this->start = $start->format(\DateTime::ATOM);
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
     * @return smsDeliveryObject
     */
    public function setMessageId($messageId)
    {
      $this->messageId = $messageId;
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
     * @return smsDeliveryObject
     */
    public function setDeliveryType($deliveryType)
    {
      $this->deliveryType = $deliveryType;
      return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
      return $this->status;
    }

    /**
     * @param string $status
     * @return smsDeliveryObject
     */
    public function setStatus($status)
    {
      $this->status = $status;
      return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
      return $this->content;
    }

    /**
     * @param string $content
     * @return smsDeliveryObject
     */
    public function setContent($content)
    {
      $this->content = $content;
      return $this;
    }

    /**
     * @return deliveryRecipientObject[]
     */
    public function getRecipients()
    {
      return $this->recipients;
    }

    /**
     * @param deliveryRecipientObject[] $recipients
     * @return smsDeliveryObject
     */
    public function setRecipients(array $recipients)
    {
      $this->recipients = $recipients;
      return $this;
    }

    /**
     * @return smsDeliveryContactsObject
     */
    public function getContactRecipients()
    {
      return $this->contactRecipients;
    }

    /**
     * @param smsDeliveryContactsObject $contactRecipients
     * @return smsDeliveryObject
     */
    public function setContactRecipients($contactRecipients)
    {
      $this->contactRecipients = $contactRecipients;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getKeywords()
    {
      return $this->keywords;
    }

    /**
     * @param string[] $keywords
     * @return smsDeliveryObject
     */
    public function setKeywords(array $keywords)
    {
      $this->keywords = $keywords;
      return $this;
    }

    /**
     * @return smsMessageFieldObject[]
     */
    public function getFields()
    {
      return $this->fields;
    }

    /**
     * @param smsMessageFieldObject[] $fields
     * @return smsDeliveryObject
     */
    public function setFields(array $fields)
    {
      $this->fields = $fields;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSends()
    {
      return $this->numSends;
    }

    /**
     * @param int $numSends
     * @return smsDeliveryObject
     */
    public function setNumSends($numSends)
    {
      $this->numSends = $numSends;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumDeliveries()
    {
      return $this->numDeliveries;
    }

    /**
     * @param int $numDeliveries
     * @return smsDeliveryObject
     */
    public function setNumDeliveries($numDeliveries)
    {
      $this->numDeliveries = $numDeliveries;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumIncoming()
    {
      return $this->numIncoming;
    }

    /**
     * @param int $numIncoming
     * @return smsDeliveryObject
     */
    public function setNumIncoming($numIncoming)
    {
      $this->numIncoming = $numIncoming;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumBounces()
    {
      return $this->numBounces;
    }

    /**
     * @param int $numBounces
     * @return smsDeliveryObject
     */
    public function setNumBounces($numBounces)
    {
      $this->numBounces = $numBounces;
      return $this;
    }

    /**
     * @return float
     */
    public function getDeliveryRate()
    {
      return $this->deliveryRate;
    }

    /**
     * @param float $deliveryRate
     * @return smsDeliveryObject
     */
    public function setDeliveryRate($deliveryRate)
    {
      $this->deliveryRate = $deliveryRate;
      return $this;
    }

}
