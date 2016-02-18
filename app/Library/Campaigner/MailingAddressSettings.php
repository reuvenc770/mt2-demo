<?php
namespace App\Library\Campaigner;
class MailingAddressSettings
{

    /**
     * @var boolean $IncludeMailingAddress
     */
    protected $IncludeMailingAddress = null;

    /**
     * @var string $MailingAddress
     */
    protected $MailingAddress = null;

    /**
     * @param boolean $IncludeMailingAddress
     * @param string $MailingAddress
     */
    public function __construct($IncludeMailingAddress, $MailingAddress)
    {
      $this->IncludeMailingAddress = $IncludeMailingAddress;
      $this->MailingAddress = $MailingAddress;
    }

    /**
     * @return boolean
     */
    public function getIncludeMailingAddress()
    {
      return $this->IncludeMailingAddress;
    }

    /**
     * @param boolean $IncludeMailingAddress
     * @return MailingAddressSettings
     */
    public function setIncludeMailingAddress($IncludeMailingAddress)
    {
      $this->IncludeMailingAddress = $IncludeMailingAddress;
      return $this;
    }

    /**
     * @return string
     */
    public function getMailingAddress()
    {
      return $this->MailingAddress;
    }

    /**
     * @param string $MailingAddress
     * @return MailingAddressSettings
     */
    public function setMailingAddress($MailingAddress)
    {
      $this->MailingAddress = $MailingAddress;
      return $this;
    }

}
