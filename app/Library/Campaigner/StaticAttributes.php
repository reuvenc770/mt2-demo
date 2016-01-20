<?php
namespace App\Library\Campaigner;
class StaticAttributes
{

    /**
     * @var string $FirstName
     */
    protected $FirstName = null;

    /**
     * @var string $LastName
     */
    protected $LastName = null;

    /**
     * @var string $Email
     */
    protected $Email = null;

    /**
     * @var string $PhoneNumber
     */
    protected $PhoneNumber = null;

    /**
     * @var string $Fax
     */
    protected $Fax = null;

    /**
     * @var string $EmailFormat
     */
    protected $EmailFormat = null;

    /**
     * @var boolean $IsTestContact
     */
    protected $IsTestContact = null;

    /**
     * @param string $FirstName
     * @param string $LastName
     * @param string $Email
     * @param string $PhoneNumber
     * @param string $Fax
     * @param string $EmailFormat
     * @param boolean $IsTestContact
     */
    public function __construct($FirstName, $LastName, $Email, $PhoneNumber, $Fax, $EmailFormat, $IsTestContact)
    {
      $this->FirstName = $FirstName;
      $this->LastName = $LastName;
      $this->Email = $Email;
      $this->PhoneNumber = $PhoneNumber;
      $this->Fax = $Fax;
      $this->EmailFormat = $EmailFormat;
      $this->IsTestContact = $IsTestContact;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
      return $this->FirstName;
    }

    /**
     * @param string $FirstName
     * @return StaticAttributes
     */
    public function setFirstName($FirstName)
    {
      $this->FirstName = $FirstName;
      return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
      return $this->LastName;
    }

    /**
     * @param string $LastName
     * @return StaticAttributes
     */
    public function setLastName($LastName)
    {
      $this->LastName = $LastName;
      return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
      return $this->Email;
    }

    /**
     * @param string $Email
     * @return StaticAttributes
     */
    public function setEmail($Email)
    {
      $this->Email = $Email;
      return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
      return $this->PhoneNumber;
    }

    /**
     * @param string $PhoneNumber
     * @return StaticAttributes
     */
    public function setPhoneNumber($PhoneNumber)
    {
      $this->PhoneNumber = $PhoneNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getFax()
    {
      return $this->Fax;
    }

    /**
     * @param string $Fax
     * @return StaticAttributes
     */
    public function setFax($Fax)
    {
      $this->Fax = $Fax;
      return $this;
    }

    /**
     * @return string
     */
    public function getEmailFormat()
    {
      return $this->EmailFormat;
    }

    /**
     * @param string $EmailFormat
     * @return StaticAttributes
     */
    public function setEmailFormat($EmailFormat)
    {
      $this->EmailFormat = $EmailFormat;
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
     * @return StaticAttributes
     */
    public function setIsTestContact($IsTestContact)
    {
      $this->IsTestContact = $IsTestContact;
      return $this;
    }

}
