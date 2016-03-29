<?php
namespace App\Library\Campaigner;
class ContactData
{

    /**
     * @var ContactKey $ContactKey
     */
    protected $ContactKey = null;

    /**
     * @var NullableElement $EmailAddress
     */
    protected $EmailAddress = null;

    /**
     * @var NullableElement $FirstName
     */
    protected $FirstName = null;

    /**
     * @var NullableElement $LastName
     */
    protected $LastName = null;

    /**
     * @var NullableElement $PhoneNumber
     */
    protected $PhoneNumber = null;

    /**
     * @var NullableElement $Fax
     */
    protected $Fax = null;

    /**
     * @var ContactStatus $Status
     */
    protected $Status = null;

    /**
     * @var ContactMailFormat $MailFormat
     */
    protected $MailFormat = null;

    /**
     * @var boolean $IsTestContact
     */
    protected $IsTestContact = null;

    /**
     * @var ArrayOfCustomAttribute $CustomAttributes
     */
    protected $CustomAttributes = null;

    /**
     * @var ArrayOfInt $AddToGroup
     */
    protected $AddToGroup = null;

    /**
     * @var ArrayOfInt $RemoveFromGroup
     */
    protected $RemoveFromGroup = null;

    /**
     * @param ContactKey $ContactKey
     * @param NullableElement $EmailAddress
     * @param NullableElement $FirstName
     * @param NullableElement $LastName
     * @param NullableElement $PhoneNumber
     * @param NullableElement $Fax
     * @param ArrayOfCustomAttribute $CustomAttributes
     * @param ArrayOfInt $AddToGroup
     * @param ArrayOfInt $RemoveFromGroup
     */
    public function __construct($ContactKey, $EmailAddress, $FirstName, $LastName, $PhoneNumber, $Fax, $CustomAttributes, $AddToGroup, $RemoveFromGroup)
    {
      $this->ContactKey = $ContactKey;
      $this->EmailAddress = $EmailAddress;
      $this->FirstName = $FirstName;
      $this->LastName = $LastName;
      $this->PhoneNumber = $PhoneNumber;
      $this->Fax = $Fax;
      $this->CustomAttributes = $CustomAttributes;
      $this->AddToGroup = $AddToGroup;
      $this->RemoveFromGroup = $RemoveFromGroup;
    }

    /**
     * @return ContactKey
     */
    public function getContactKey()
    {
      return $this->ContactKey;
    }

    /**
     * @param ContactKey $ContactKey
     * @return ContactData
     */
    public function setContactKey($ContactKey)
    {
      $this->ContactKey = $ContactKey;
      return $this;
    }

    /**
     * @return NullableElement
     */
    public function getEmailAddress()
    {
      return $this->EmailAddress;
    }

    /**
     * @param NullableElement $EmailAddress
     * @return ContactData
     */
    public function setEmailAddress($EmailAddress)
    {
      $this->EmailAddress = $EmailAddress;
      return $this;
    }

    /**
     * @return NullableElement
     */
    public function getFirstName()
    {
      return $this->FirstName;
    }

    /**
     * @param NullableElement $FirstName
     * @return ContactData
     */
    public function setFirstName($FirstName)
    {
      $this->FirstName = $FirstName;
      return $this;
    }

    /**
     * @return NullableElement
     */
    public function getLastName()
    {
      return $this->LastName;
    }

    /**
     * @param NullableElement $LastName
     * @return ContactData
     */
    public function setLastName($LastName)
    {
      $this->LastName = $LastName;
      return $this;
    }

    /**
     * @return NullableElement
     */
    public function getPhoneNumber()
    {
      return $this->PhoneNumber;
    }

    /**
     * @param NullableElement $PhoneNumber
     * @return ContactData
     */
    public function setPhoneNumber($PhoneNumber)
    {
      $this->PhoneNumber = $PhoneNumber;
      return $this;
    }

    /**
     * @return NullableElement
     */
    public function getFax()
    {
      return $this->Fax;
    }

    /**
     * @param NullableElement $Fax
     * @return ContactData
     */
    public function setFax($Fax)
    {
      $this->Fax = $Fax;
      return $this;
    }

    /**
     * @return ContactStatus
     */
    public function getStatus()
    {
      return $this->Status;
    }

    /**
     * @param ContactStatus $Status
     * @return ContactData
     */
    public function setStatus($Status)
    {
      $this->Status = $Status;
      return $this;
    }

    /**
     * @return ContactMailFormat
     */
    public function getMailFormat()
    {
      return $this->MailFormat;
    }

    /**
     * @param ContactMailFormat $MailFormat
     * @return ContactData
     */
    public function setMailFormat($MailFormat)
    {
      $this->MailFormat = $MailFormat;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsTestContact()
    {
      return $this->IsTestContact;
    }

    /**
     * @param boolean $IsTestContact
     * @return ContactData
     */
    public function setIsTestContact($IsTestContact)
    {
      $this->IsTestContact = $IsTestContact;
      return $this;
    }

    /**
     * @return ArrayOfCustomAttribute
     */
    public function getCustomAttributes()
    {
      return $this->CustomAttributes;
    }

    /**
     * @param ArrayOfCustomAttribute $CustomAttributes
     * @return ContactData
     */
    public function setCustomAttributes($CustomAttributes)
    {
      $this->CustomAttributes = $CustomAttributes;
      return $this;
    }

    /**
     * @return ArrayOfInt
     */
    public function getAddToGroup()
    {
      return $this->AddToGroup;
    }

    /**
     * @param ArrayOfInt $AddToGroup
     * @return ContactData
     */
    public function setAddToGroup($AddToGroup)
    {
      $this->AddToGroup = $AddToGroup;
      return $this;
    }

    /**
     * @return ArrayOfInt
     */
    public function getRemoveFromGroup()
    {
      return $this->RemoveFromGroup;
    }

    /**
     * @param ArrayOfInt $RemoveFromGroup
     * @return ContactData
     */
    public function setRemoveFromGroup($RemoveFromGroup)
    {
      $this->RemoveFromGroup = $RemoveFromGroup;
      return $this;
    }

}
