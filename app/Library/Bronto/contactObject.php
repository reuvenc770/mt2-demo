<?php

class contactObject
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var string $email
     */
    protected $email = null;

    /**
     * @var string $mobileNumber
     */
    protected $mobileNumber = null;

    /**
     * @var string $status
     */
    protected $status = null;

    /**
     * @var string $msgPref
     */
    protected $msgPref = null;

    /**
     * @var string $source
     */
    protected $source = null;

    /**
     * @var string $customSource
     */
    protected $customSource = null;

    /**
     * @var \DateTime $created
     */
    protected $created = null;

    /**
     * @var \DateTime $modified
     */
    protected $modified = null;

    /**
     * @var boolean $deleted
     */
    protected $deleted = null;

    /**
     * @var string[] $listIds
     */
    protected $listIds = null;

    /**
     * @var string[] $segmentIds
     */
    protected $segmentIds = null;

    /**
     * @var contactField[] $fields
     */
    protected $fields = null;

    /**
     * @var string[] $SMSKeywordIDs
     */
    protected $SMSKeywordIDs = null;

    /**
     * @var int $numSends
     */
    protected $numSends = null;

    /**
     * @var int $numBounces
     */
    protected $numBounces = null;

    /**
     * @var int $numOpens
     */
    protected $numOpens = null;

    /**
     * @var int $numClicks
     */
    protected $numClicks = null;

    /**
     * @var int $numConversions
     */
    protected $numConversions = null;

    /**
     * @var float $conversionAmount
     */
    protected $conversionAmount = null;

    /**
     * @var readOnlyContactData $readOnlyContactData
     */
    protected $readOnlyContactData = null;

    /**
     * @param string $id
     * @param string $email
     * @param string $mobileNumber
     * @param string $status
     * @param string $msgPref
     * @param string $source
     * @param string $customSource
     * @param \DateTime $created
     * @param \DateTime $modified
     * @param boolean $deleted
     * @param int $numSends
     * @param int $numBounces
     * @param int $numOpens
     * @param int $numClicks
     * @param int $numConversions
     * @param float $conversionAmount
     * @param readOnlyContactData $readOnlyContactData
     */
    public function __construct($id, $email, $mobileNumber, $status, $msgPref, $source, $customSource, \DateTime $created, \DateTime $modified, $deleted, $numSends, $numBounces, $numOpens, $numClicks, $numConversions, $conversionAmount, $readOnlyContactData)
    {
      $this->id = $id;
      $this->email = $email;
      $this->mobileNumber = $mobileNumber;
      $this->status = $status;
      $this->msgPref = $msgPref;
      $this->source = $source;
      $this->customSource = $customSource;
      $this->created = $created->format(\DateTime::ATOM);
      $this->modified = $modified->format(\DateTime::ATOM);
      $this->deleted = $deleted;
      $this->numSends = $numSends;
      $this->numBounces = $numBounces;
      $this->numOpens = $numOpens;
      $this->numClicks = $numClicks;
      $this->numConversions = $numConversions;
      $this->conversionAmount = $conversionAmount;
      $this->readOnlyContactData = $readOnlyContactData;
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
     * @return contactObject
     */
    public function setId($id)
    {
      $this->id = $id;
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
     * @return contactObject
     */
    public function setEmail($email)
    {
      $this->email = $email;
      return $this;
    }

    /**
     * @return string
     */
    public function getMobileNumber()
    {
      return $this->mobileNumber;
    }

    /**
     * @param string $mobileNumber
     * @return contactObject
     */
    public function setMobileNumber($mobileNumber)
    {
      $this->mobileNumber = $mobileNumber;
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
     * @return contactObject
     */
    public function setStatus($status)
    {
      $this->status = $status;
      return $this;
    }

    /**
     * @return string
     */
    public function getMsgPref()
    {
      return $this->msgPref;
    }

    /**
     * @param string $msgPref
     * @return contactObject
     */
    public function setMsgPref($msgPref)
    {
      $this->msgPref = $msgPref;
      return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
      return $this->source;
    }

    /**
     * @param string $source
     * @return contactObject
     */
    public function setSource($source)
    {
      $this->source = $source;
      return $this;
    }

    /**
     * @return string
     */
    public function getCustomSource()
    {
      return $this->customSource;
    }

    /**
     * @param string $customSource
     * @return contactObject
     */
    public function setCustomSource($customSource)
    {
      $this->customSource = $customSource;
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
     * @return contactObject
     */
    public function setCreated(\DateTime $created)
    {
      $this->created = $created->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getModified()
    {
      if ($this->modified == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->modified);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $modified
     * @return contactObject
     */
    public function setModified(\DateTime $modified)
    {
      $this->modified = $modified->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDeleted()
    {
      return $this->deleted;
    }

    /**
     * @param boolean $deleted
     * @return contactObject
     */
    public function setDeleted($deleted)
    {
      $this->deleted = $deleted;
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
     * @return contactObject
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
     * @return contactObject
     */
    public function setSegmentIds(array $segmentIds)
    {
      $this->segmentIds = $segmentIds;
      return $this;
    }

    /**
     * @return contactField[]
     */
    public function getFields()
    {
      return $this->fields;
    }

    /**
     * @param contactField[] $fields
     * @return contactObject
     */
    public function setFields(array $fields)
    {
      $this->fields = $fields;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getSMSKeywordIDs()
    {
      return $this->SMSKeywordIDs;
    }

    /**
     * @param string[] $SMSKeywordIDs
     * @return contactObject
     */
    public function setSMSKeywordIDs(array $SMSKeywordIDs)
    {
      $this->SMSKeywordIDs = $SMSKeywordIDs;
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
     * @return contactObject
     */
    public function setNumSends($numSends)
    {
      $this->numSends = $numSends;
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
     * @return contactObject
     */
    public function setNumBounces($numBounces)
    {
      $this->numBounces = $numBounces;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumOpens()
    {
      return $this->numOpens;
    }

    /**
     * @param int $numOpens
     * @return contactObject
     */
    public function setNumOpens($numOpens)
    {
      $this->numOpens = $numOpens;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumClicks()
    {
      return $this->numClicks;
    }

    /**
     * @param int $numClicks
     * @return contactObject
     */
    public function setNumClicks($numClicks)
    {
      $this->numClicks = $numClicks;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumConversions()
    {
      return $this->numConversions;
    }

    /**
     * @param int $numConversions
     * @return contactObject
     */
    public function setNumConversions($numConversions)
    {
      $this->numConversions = $numConversions;
      return $this;
    }

    /**
     * @return float
     */
    public function getConversionAmount()
    {
      return $this->conversionAmount;
    }

    /**
     * @param float $conversionAmount
     * @return contactObject
     */
    public function setConversionAmount($conversionAmount)
    {
      $this->conversionAmount = $conversionAmount;
      return $this;
    }

    /**
     * @return readOnlyContactData
     */
    public function getReadOnlyContactData()
    {
      return $this->readOnlyContactData;
    }

    /**
     * @param readOnlyContactData $readOnlyContactData
     * @return contactObject
     */
    public function setReadOnlyContactData($readOnlyContactData)
    {
      $this->readOnlyContactData = $readOnlyContactData;
      return $this;
    }

}
