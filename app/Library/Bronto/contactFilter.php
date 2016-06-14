<?php

class contactFilter
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
     * @var stringValue[] $email
     */
    protected $email = null;

    /**
     * @var stringValue[] $mobileNumber
     */
    protected $mobileNumber = null;

    /**
     * @var string[] $status
     */
    protected $status = null;

    /**
     * @var dateValue[] $created
     */
    protected $created = null;

    /**
     * @var dateValue[] $modified
     */
    protected $modified = null;

    /**
     * @var string[] $listId
     */
    protected $listId = null;

    /**
     * @var string[] $segmentId
     */
    protected $segmentId = null;

    /**
     * @var string[] $SMSKeywordID
     */
    protected $SMSKeywordID = null;

    /**
     * @var string[] $msgPref
     */
    protected $msgPref = null;

    /**
     * @var string[] $source
     */
    protected $source = null;

    /**
     * @var stringValue[] $customSource
     */
    protected $customSource = null;

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
     * @return contactFilter
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
     * @return contactFilter
     */
    public function setId(array $id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return stringValue[]
     */
    public function getEmail()
    {
      return $this->email;
    }

    /**
     * @param stringValue[] $email
     * @return contactFilter
     */
    public function setEmail(array $email)
    {
      $this->email = $email;
      return $this;
    }

    /**
     * @return stringValue[]
     */
    public function getMobileNumber()
    {
      return $this->mobileNumber;
    }

    /**
     * @param stringValue[] $mobileNumber
     * @return contactFilter
     */
    public function setMobileNumber(array $mobileNumber)
    {
      $this->mobileNumber = $mobileNumber;
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
     * @return contactFilter
     */
    public function setStatus(array $status)
    {
      $this->status = $status;
      return $this;
    }

    /**
     * @return dateValue[]
     */
    public function getCreated()
    {
      return $this->created;
    }

    /**
     * @param dateValue[] $created
     * @return contactFilter
     */
    public function setCreated(array $created)
    {
      $this->created = $created;
      return $this;
    }

    /**
     * @return dateValue[]
     */
    public function getModified()
    {
      return $this->modified;
    }

    /**
     * @param dateValue[] $modified
     * @return contactFilter
     */
    public function setModified(array $modified)
    {
      $this->modified = $modified;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getListId()
    {
      return $this->listId;
    }

    /**
     * @param string[] $listId
     * @return contactFilter
     */
    public function setListId(array $listId)
    {
      $this->listId = $listId;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getSegmentId()
    {
      return $this->segmentId;
    }

    /**
     * @param string[] $segmentId
     * @return contactFilter
     */
    public function setSegmentId(array $segmentId)
    {
      $this->segmentId = $segmentId;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getSMSKeywordID()
    {
      return $this->SMSKeywordID;
    }

    /**
     * @param string[] $SMSKeywordID
     * @return contactFilter
     */
    public function setSMSKeywordID(array $SMSKeywordID)
    {
      $this->SMSKeywordID = $SMSKeywordID;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getMsgPref()
    {
      return $this->msgPref;
    }

    /**
     * @param string[] $msgPref
     * @return contactFilter
     */
    public function setMsgPref(array $msgPref)
    {
      $this->msgPref = $msgPref;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getSource()
    {
      return $this->source;
    }

    /**
     * @param string[] $source
     * @return contactFilter
     */
    public function setSource(array $source)
    {
      $this->source = $source;
      return $this;
    }

    /**
     * @return stringValue[]
     */
    public function getCustomSource()
    {
      return $this->customSource;
    }

    /**
     * @param stringValue[] $customSource
     * @return contactFilter
     */
    public function setCustomSource(array $customSource)
    {
      $this->customSource = $customSource;
      return $this;
    }

}
