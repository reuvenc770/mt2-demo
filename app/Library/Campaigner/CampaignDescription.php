<?php
namespace App\Library\Campaigner;
class CampaignDescription
{

    /**
     * @var int $Id
     */
    protected $Id = null;

    /**
     * @var string $Name
     */
    protected $Name = null;

    /**
     * @var CampaignStatus $Status
     */
    protected $Status = null;

    /**
     * @var CampaignType $Type
     */
    protected $Type = null;

    /**
     * @var string $FromName
     */
    protected $FromName = null;

    /**
     * @var string $FromEmail
     */
    protected $FromEmail = null;

    /**
     * @var string $Subject
     */
    protected $Subject = null;

    /**
     * @var int $ProjectId
     */
    protected $ProjectId = null;

    /**
     * @var CampaignFormat $Format
     */
    protected $Format = null;

    /**
     * @var \DateTime $CreatedDate
     */
    protected $CreatedDate = null;

    /**
     * @var \DateTime $LastModifiedDate
     */
    protected $LastModifiedDate = null;

    /**
     * @param int $Id
     * @param string $Name
     * @param CampaignStatus $Status
     * @param CampaignType $Type
     * @param string $FromName
     * @param string $FromEmail
     * @param string $Subject
     * @param int $ProjectId
     */
    public function __construct($Id, $Name, $Status, $Type, $FromName, $FromEmail, $Subject, $ProjectId)
    {
      $this->Id = $Id;
      $this->Name = $Name;
      $this->Status = $Status;
      $this->Type = $Type;
      $this->FromName = $FromName;
      $this->FromEmail = $FromEmail;
      $this->Subject = $Subject;
      $this->ProjectId = $ProjectId;
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
     * @return CampaignDescription
     */
    public function setId($Id)
    {
      $this->Id = $Id;
      return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return $this->Name;
    }

    /**
     * @param string $Name
     * @return CampaignDescription
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return CampaignStatus
     */
    public function getStatus()
    {
      return $this->Status;
    }

    /**
     * @param CampaignStatus $Status
     * @return CampaignDescription
     */
    public function setStatus($Status)
    {
      $this->Status = $Status;
      return $this;
    }

    /**
     * @return CampaignType
     */
    public function getType()
    {
      return $this->Type;
    }

    /**
     * @param CampaignType $Type
     * @return CampaignDescription
     */
    public function setType($Type)
    {
      $this->Type = $Type;
      return $this;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
      return $this->FromName;
    }

    /**
     * @param string $FromName
     * @return CampaignDescription
     */
    public function setFromName($FromName)
    {
      $this->FromName = $FromName;
      return $this;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
      return $this->FromEmail;
    }

    /**
     * @param string $FromEmail
     * @return CampaignDescription
     */
    public function setFromEmail($FromEmail)
    {
      $this->FromEmail = $FromEmail;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
      return $this->Subject;
    }

    /**
     * @param string $Subject
     * @return CampaignDescription
     */
    public function setSubject($Subject)
    {
      $this->Subject = $Subject;
      return $this;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
      return $this->ProjectId;
    }

    /**
     * @param int $ProjectId
     * @return CampaignDescription
     */
    public function setProjectId($ProjectId)
    {
      $this->ProjectId = $ProjectId;
      return $this;
    }

    /**
     * @return CampaignFormat
     */
    public function getFormat()
    {
      return $this->Format;
    }

    /**
     * @param CampaignFormat $Format
     * @return CampaignDescription
     */
    public function setFormat($Format)
    {
      $this->Format = $Format;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
      if ($this->CreatedDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->CreatedDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $CreatedDate
     * @return CampaignDescription
     */
    public function setCreatedDate(\DateTime $CreatedDate)
    {
      $this->CreatedDate = $CreatedDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastModifiedDate()
    {
      if ($this->LastModifiedDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->LastModifiedDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $LastModifiedDate
     * @return CampaignDescription
     */
    public function setLastModifiedDate(\DateTime $LastModifiedDate)
    {
      $this->LastModifiedDate = $LastModifiedDate->format(\DateTime::ATOM);
      return $this;
    }

}
