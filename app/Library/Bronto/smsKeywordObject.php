<?php
namespace App\Library\Bronto;
class smsKeywordObject
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $description
     */
    protected $description = null;

    /**
     * @var int $subscriberCount
     */
    protected $subscriberCount = null;

    /**
     * @var int $frequencyCap
     */
    protected $frequencyCap = null;

    /**
     * @var \DateTime $dateCreated
     */
    protected $dateCreated = null;

    /**
     * @var \DateTime $scheduledDeleteDate
     */
    protected $scheduledDeleteDate = null;

    /**
     * @var string $confirmationMessage
     */
    protected $confirmationMessage = null;

    /**
     * @var string $messageContent
     */
    protected $messageContent = null;

    /**
     * @var string $keywordType
     */
    protected $keywordType = null;

    /**
     * @param string $id
     * @param string $name
     * @param string $description
     * @param int $subscriberCount
     * @param int $frequencyCap
     * @param \DateTime $dateCreated
     * @param \DateTime $scheduledDeleteDate
     * @param string $confirmationMessage
     * @param string $messageContent
     * @param string $keywordType
     */
    public function __construct($id, $name, $description, $subscriberCount, $frequencyCap, \DateTime $dateCreated, \DateTime $scheduledDeleteDate, $confirmationMessage, $messageContent, $keywordType)
    {
      $this->id = $id;
      $this->name = $name;
      $this->description = $description;
      $this->subscriberCount = $subscriberCount;
      $this->frequencyCap = $frequencyCap;
      $this->dateCreated = $dateCreated->format(\DateTime::ATOM);
      $this->scheduledDeleteDate = $scheduledDeleteDate->format(\DateTime::ATOM);
      $this->confirmationMessage = $confirmationMessage;
      $this->messageContent = $messageContent;
      $this->keywordType = $keywordType;
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
     * @return smsKeywordObject
     */
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return $this->name;
    }

    /**
     * @param string $name
     * @return smsKeywordObject
     */
    public function setName($name)
    {
      $this->name = $name;
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
     * @return smsKeywordObject
     */
    public function setDescription($description)
    {
      $this->description = $description;
      return $this;
    }

    /**
     * @return int
     */
    public function getSubscriberCount()
    {
      return $this->subscriberCount;
    }

    /**
     * @param int $subscriberCount
     * @return smsKeywordObject
     */
    public function setSubscriberCount($subscriberCount)
    {
      $this->subscriberCount = $subscriberCount;
      return $this;
    }

    /**
     * @return int
     */
    public function getFrequencyCap()
    {
      return $this->frequencyCap;
    }

    /**
     * @param int $frequencyCap
     * @return smsKeywordObject
     */
    public function setFrequencyCap($frequencyCap)
    {
      $this->frequencyCap = $frequencyCap;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
      if ($this->dateCreated == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->dateCreated);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $dateCreated
     * @return smsKeywordObject
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
      $this->dateCreated = $dateCreated->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getScheduledDeleteDate()
    {
      if ($this->scheduledDeleteDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->scheduledDeleteDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $scheduledDeleteDate
     * @return smsKeywordObject
     */
    public function setScheduledDeleteDate(\DateTime $scheduledDeleteDate)
    {
      $this->scheduledDeleteDate = $scheduledDeleteDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationMessage()
    {
      return $this->confirmationMessage;
    }

    /**
     * @param string $confirmationMessage
     * @return smsKeywordObject
     */
    public function setConfirmationMessage($confirmationMessage)
    {
      $this->confirmationMessage = $confirmationMessage;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageContent()
    {
      return $this->messageContent;
    }

    /**
     * @param string $messageContent
     * @return smsKeywordObject
     */
    public function setMessageContent($messageContent)
    {
      $this->messageContent = $messageContent;
      return $this;
    }

    /**
     * @return string
     */
    public function getKeywordType()
    {
      return $this->keywordType;
    }

    /**
     * @param string $keywordType
     * @return smsKeywordObject
     */
    public function setKeywordType($keywordType)
    {
      $this->keywordType = $keywordType;
      return $this;
    }

}
