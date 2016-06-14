<?php
namespace App\Library\Bronto;
class accountObject
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
     * @var string $status
     */
    protected $status = null;

    /**
     * @var generalSettings $generalSettings
     */
    protected $generalSettings = null;

    /**
     * @var contactInformation $contactInformation
     */
    protected $contactInformation = null;

    /**
     * @var formatSettings $formatSettings
     */
    protected $formatSettings = null;

    /**
     * @var brandingSettings $brandingSettings
     */
    protected $brandingSettings = null;

    /**
     * @var repliesSettings $repliesSettings
     */
    protected $repliesSettings = null;

    /**
     * @var accountAllocations $allocations
     */
    protected $allocations = null;

    /**
     * @param string $id
     * @param string $name
     * @param string $status
     * @param generalSettings $generalSettings
     * @param contactInformation $contactInformation
     * @param formatSettings $formatSettings
     * @param brandingSettings $brandingSettings
     * @param repliesSettings $repliesSettings
     * @param accountAllocations $allocations
     */
    public function __construct($id, $name, $status, $generalSettings, $contactInformation, $formatSettings, $brandingSettings, $repliesSettings, $allocations)
    {
      $this->id = $id;
      $this->name = $name;
      $this->status = $status;
      $this->generalSettings = $generalSettings;
      $this->contactInformation = $contactInformation;
      $this->formatSettings = $formatSettings;
      $this->brandingSettings = $brandingSettings;
      $this->repliesSettings = $repliesSettings;
      $this->allocations = $allocations;
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
     * @return accountObject
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
     * @return accountObject
     */
    public function setName($name)
    {
      $this->name = $name;
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
     * @return accountObject
     */
    public function setStatus($status)
    {
      $this->status = $status;
      return $this;
    }

    /**
     * @return generalSettings
     */
    public function getGeneralSettings()
    {
      return $this->generalSettings;
    }

    /**
     * @param generalSettings $generalSettings
     * @return accountObject
     */
    public function setGeneralSettings($generalSettings)
    {
      $this->generalSettings = $generalSettings;
      return $this;
    }

    /**
     * @return contactInformation
     */
    public function getContactInformation()
    {
      return $this->contactInformation;
    }

    /**
     * @param contactInformation $contactInformation
     * @return accountObject
     */
    public function setContactInformation($contactInformation)
    {
      $this->contactInformation = $contactInformation;
      return $this;
    }

    /**
     * @return formatSettings
     */
    public function getFormatSettings()
    {
      return $this->formatSettings;
    }

    /**
     * @param formatSettings $formatSettings
     * @return accountObject
     */
    public function setFormatSettings($formatSettings)
    {
      $this->formatSettings = $formatSettings;
      return $this;
    }

    /**
     * @return brandingSettings
     */
    public function getBrandingSettings()
    {
      return $this->brandingSettings;
    }

    /**
     * @param brandingSettings $brandingSettings
     * @return accountObject
     */
    public function setBrandingSettings($brandingSettings)
    {
      $this->brandingSettings = $brandingSettings;
      return $this;
    }

    /**
     * @return repliesSettings
     */
    public function getRepliesSettings()
    {
      return $this->repliesSettings;
    }

    /**
     * @param repliesSettings $repliesSettings
     * @return accountObject
     */
    public function setRepliesSettings($repliesSettings)
    {
      $this->repliesSettings = $repliesSettings;
      return $this;
    }

    /**
     * @return accountAllocations
     */
    public function getAllocations()
    {
      return $this->allocations;
    }

    /**
     * @param accountAllocations $allocations
     * @return accountObject
     */
    public function setAllocations($allocations)
    {
      $this->allocations = $allocations;
      return $this;
    }

}
