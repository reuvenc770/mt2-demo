<?php
namespace App\Library\Campaigner;
class TestContact
{

    /**
     * @var ContactKey $ContactKey
     */
    protected $ContactKey = null;

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
     * @var string $Phone
     */
    protected $Phone = null;

    /**
     * @var string $Fax
     */
    protected $Fax = null;

    /**
     * @var ContactMailFormat $EmailFormat
     */
    protected $EmailFormat = null;

    /**
     * @var ContactStatus $Status
     */
    protected $Status = null;

    /**
     * @param ContactKey $ContactKey
     * @param string $FirstName
     * @param string $LastName
     * @param string $Email
     * @param string $Phone
     * @param string $Fax
     */
    public function __construct($ContactKey, $FirstName, $LastName, $Email, $Phone, $Fax)
    {
      $this->ContactKey = $ContactKey;
      $this->FirstName = $FirstName;
      $this->LastName = $LastName;
      $this->Email = $Email;
      $this->Phone = $Phone;
      $this->Fax = $Fax;
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
     * @return TestContact
     */
    public function setContactKey($ContactKey)
    {
      $this->ContactKey = $ContactKey;
      return $this;
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
     * @return TestContact
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
     * @return TestContact
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
     * @return TestContact
     */
    public function setEmail($Email)
    {
      $this->Email = $Email;
      return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
      return $this->Phone;
    }

    /**
     * @param string $Phone
     * @return TestContact
     */
    public function setPhone($Phone)
    {
      $this->Phone = $Phone;
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
     * @return TestContact
     */
    public function setFax($Fax)
    {
      $this->Fax = $Fax;
      return $this;
    }

    /**
     * @return ContactMailFormat
     */
    public function getEmailFormat()
    {
      return $this->EmailFormat;
    }

    /**
     * @param ContactMailFormat $EmailFormat
     * @return TestContact
     */
    public function setEmailFormat($EmailFormat)
    {
      $this->EmailFormat = $EmailFormat;
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
     * @return TestContact
     */
    public function setStatus($Status)
    {
      $this->Status = $Status;
      return $this;
    }

}
