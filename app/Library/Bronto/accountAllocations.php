<?php
namespace App\Library\Bronto;
class accountAllocations
{

    /**
     * @var boolean $canExceedAllocation
     */
    protected $canExceedAllocation = null;

    /**
     * @var boolean $canExceedSmsAllocation
     */
    protected $canExceedSmsAllocation = null;

    /**
     * @var int $emails
     */
    protected $emails = null;

    /**
     * @var int $contacts
     */
    protected $contacts = null;

    /**
     * @var int $hosting
     */
    protected $hosting = null;

    /**
     * @var int $logins
     */
    protected $logins = null;

    /**
     * @var boolean $api
     */
    protected $api = null;

    /**
     * @var int $fields
     */
    protected $fields = null;

    /**
     * @var \DateTime $startDate
     */
    protected $startDate = null;

    /**
     * @var int $periodFrequency
     */
    protected $periodFrequency = null;

    /**
     * @var string $bundle
     */
    protected $bundle = null;

    /**
     * @var boolean $defaultTemplates
     */
    protected $defaultTemplates = null;

    /**
     * @var boolean $branding
     */
    protected $branding = null;

    /**
     * @param boolean $canExceedAllocation
     * @param boolean $canExceedSmsAllocation
     * @param int $emails
     * @param int $contacts
     * @param int $hosting
     * @param int $logins
     * @param boolean $api
     * @param int $fields
     * @param \DateTime $startDate
     * @param int $periodFrequency
     * @param string $bundle
     * @param boolean $defaultTemplates
     * @param boolean $branding
     */
    public function __construct($canExceedAllocation, $canExceedSmsAllocation, $emails, $contacts, $hosting, $logins, $api, $fields, \DateTime $startDate, $periodFrequency, $bundle, $defaultTemplates, $branding)
    {
      $this->canExceedAllocation = $canExceedAllocation;
      $this->canExceedSmsAllocation = $canExceedSmsAllocation;
      $this->emails = $emails;
      $this->contacts = $contacts;
      $this->hosting = $hosting;
      $this->logins = $logins;
      $this->api = $api;
      $this->fields = $fields;
      $this->startDate = $startDate->format(\DateTime::ATOM);
      $this->periodFrequency = $periodFrequency;
      $this->bundle = $bundle;
      $this->defaultTemplates = $defaultTemplates;
      $this->branding = $branding;
    }

    /**
     * @return boolean
     */
    public function getCanExceedAllocation()
    {
      return $this->canExceedAllocation;
    }

    /**
     * @param boolean $canExceedAllocation
     * @return accountAllocations
     */
    public function setCanExceedAllocation($canExceedAllocation)
    {
      $this->canExceedAllocation = $canExceedAllocation;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getCanExceedSmsAllocation()
    {
      return $this->canExceedSmsAllocation;
    }

    /**
     * @param boolean $canExceedSmsAllocation
     * @return accountAllocations
     */
    public function setCanExceedSmsAllocation($canExceedSmsAllocation)
    {
      $this->canExceedSmsAllocation = $canExceedSmsAllocation;
      return $this;
    }

    /**
     * @return int
     */
    public function getEmails()
    {
      return $this->emails;
    }

    /**
     * @param int $emails
     * @return accountAllocations
     */
    public function setEmails($emails)
    {
      $this->emails = $emails;
      return $this;
    }

    /**
     * @return int
     */
    public function getContacts()
    {
      return $this->contacts;
    }

    /**
     * @param int $contacts
     * @return accountAllocations
     */
    public function setContacts($contacts)
    {
      $this->contacts = $contacts;
      return $this;
    }

    /**
     * @return int
     */
    public function getHosting()
    {
      return $this->hosting;
    }

    /**
     * @param int $hosting
     * @return accountAllocations
     */
    public function setHosting($hosting)
    {
      $this->hosting = $hosting;
      return $this;
    }

    /**
     * @return int
     */
    public function getLogins()
    {
      return $this->logins;
    }

    /**
     * @param int $logins
     * @return accountAllocations
     */
    public function setLogins($logins)
    {
      $this->logins = $logins;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getApi()
    {
      return $this->api;
    }

    /**
     * @param boolean $api
     * @return accountAllocations
     */
    public function setApi($api)
    {
      $this->api = $api;
      return $this;
    }

    /**
     * @return int
     */
    public function getFields()
    {
      return $this->fields;
    }

    /**
     * @param int $fields
     * @return accountAllocations
     */
    public function setFields($fields)
    {
      $this->fields = $fields;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
      if ($this->startDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->startDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $startDate
     * @return accountAllocations
     */
    public function setStartDate(\DateTime $startDate)
    {
      $this->startDate = $startDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return int
     */
    public function getPeriodFrequency()
    {
      return $this->periodFrequency;
    }

    /**
     * @param int $periodFrequency
     * @return accountAllocations
     */
    public function setPeriodFrequency($periodFrequency)
    {
      $this->periodFrequency = $periodFrequency;
      return $this;
    }

    /**
     * @return string
     */
    public function getBundle()
    {
      return $this->bundle;
    }

    /**
     * @param string $bundle
     * @return accountAllocations
     */
    public function setBundle($bundle)
    {
      $this->bundle = $bundle;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDefaultTemplates()
    {
      return $this->defaultTemplates;
    }

    /**
     * @param boolean $defaultTemplates
     * @return accountAllocations
     */
    public function setDefaultTemplates($defaultTemplates)
    {
      $this->defaultTemplates = $defaultTemplates;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getBranding()
    {
      return $this->branding;
    }

    /**
     * @param boolean $branding
     * @return accountAllocations
     */
    public function setBranding($branding)
    {
      $this->branding = $branding;
      return $this;
    }

}
