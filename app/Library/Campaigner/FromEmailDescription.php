<?php
namespace App\Library\Campaigner;
class FromEmailDescription
{

    /**
     * @var int $Id
     */
    protected $Id = null;

    /**
     * @var string $EmailAddress
     */
    protected $EmailAddress = null;

    /**
     * @var FromEmailStatus $FromEmailStatus
     */
    protected $FromEmailStatus = null;

    /**
     * @param int $Id
     * @param string $EmailAddress
     * @param FromEmailStatus $FromEmailStatus
     */
    public function __construct($Id, $EmailAddress, $FromEmailStatus)
    {
      $this->Id = $Id;
      $this->EmailAddress = $EmailAddress;
      $this->FromEmailStatus = $FromEmailStatus;
    }

    /**
     * @return int
     */
    public function getId()
    {
      return $this->Id;
    }

    /**
     * @param int $Id
     * @return FromEmailDescription
     */
    public function setId($Id)
    {
      $this->Id = $Id;
      return $this;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
      return $this->EmailAddress;
    }

    /**
     * @param string $EmailAddress
     * @return FromEmailDescription
     */
    public function setEmailAddress($EmailAddress)
    {
      $this->EmailAddress = $EmailAddress;
      return $this;
    }

    /**
     * @return FromEmailStatus
     */
    public function getFromEmailStatus()
    {
      return $this->FromEmailStatus;
    }

    /**
     * @param FromEmailStatus $FromEmailStatus
     * @return FromEmailDescription
     */
    public function setFromEmailStatus($FromEmailStatus)
    {
      $this->FromEmailStatus = $FromEmailStatus;
      return $this;
    }

}
