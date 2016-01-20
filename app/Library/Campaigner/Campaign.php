<?php
namespace App\Library\Campaigner;
class Campaign
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
     * @var string $Subject
     */
    protected $Subject = null;

    /**
     * @var string $FromName
     */
    protected $FromName = null;

    /**
     * @var string $FromEmail
     */
    protected $FromEmail = null;

    /**
     * @var \DateTime $CreationDate
     */
    protected $CreationDate = null;

    /**
     * @var int $ProjectId
     */
    protected $ProjectId = null;

    /**
     * @var boolean $SentToAllContacts
     */
    protected $SentToAllContacts = null;

    /**
     * @var ArrayOfInt $SentToContactGroupIds
     */
    protected $SentToContactGroupIds = null;

    /**
     * @var ArrayOfCampaignRun $CampaignRuns
     */
    protected $CampaignRuns = null;

    /**
     * @param int $Id
     * @param string $Name
     * @param CampaignStatus $Status
     * @param CampaignType $Type
     * @param string $Subject
     * @param string $FromName
     * @param string $FromEmail
     * @param \DateTime $CreationDate
     * @param ArrayOfInt $SentToContactGroupIds
     * @param ArrayOfCampaignRun $CampaignRuns
     */
    public function __construct($Id, $Name, $Status, $Type, $Subject, $FromName, $FromEmail, \DateTime $CreationDate, $SentToContactGroupIds, $CampaignRuns)
    {
      $this->Id = $Id;
      $this->Name = $Name;
      $this->Status = $Status;
      $this->Type = $Type;
      $this->Subject = $Subject;
      $this->FromName = $FromName;
      $this->FromEmail = $FromEmail;
      $this->CreationDate = $CreationDate->format(\DateTime::ATOM);
      $this->SentToContactGroupIds = $SentToContactGroupIds;
      $this->CampaignRuns = $CampaignRuns;
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
     * @return Campaign
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
     * @return Campaign
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
     * @return Campaign
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
     * @return Campaign
     */
    public function setType($Type)
    {
      $this->Type = $Type;
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
     * @return Campaign
     */
    public function setSubject($Subject)
    {
      $this->Subject = $Subject;
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
     * @return Campaign
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
     * @return Campaign
     */
    public function setFromEmail($FromEmail)
    {
      $this->FromEmail = $FromEmail;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
      if ($this->CreationDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->CreationDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $CreationDate
     * @return Campaign
     */
    public function setCreationDate(\DateTime $CreationDate)
    {
      $this->CreationDate = $CreationDate->format(\DateTime::ATOM);
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
     * @return Campaign
     */
    public function setProjectId($ProjectId)
    {
      $this->ProjectId = $ProjectId;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getSentToAllContacts()
    {
      return $this->SentToAllContacts;
    }

    /**
     * @param boolean $SentToAllContacts
     * @return Campaign
     */
    public function setSentToAllContacts($SentToAllContacts)
    {
      $this->SentToAllContacts = $SentToAllContacts;
      return $this;
    }

    /**
     * @return ArrayOfInt
     */
    public function getSentToContactGroupIds()
    {
      return $this->SentToContactGroupIds;
    }

    /**
     * @param ArrayOfInt $SentToContactGroupIds
     * @return Campaign
     */
    public function setSentToContactGroupIds($SentToContactGroupIds)
    {
      $this->SentToContactGroupIds = $SentToContactGroupIds;
      return $this;
    }

    /**
     * @return ArrayOfCampaignRun
     */
    public function getCampaignRuns()
    {
      return $this->CampaignRuns;
    }

    /**
     * @param ArrayOfCampaignRun $CampaignRuns
     * @return Campaign
     */
    public function setCampaignRuns($CampaignRuns)
    {
      $this->CampaignRuns = $CampaignRuns;
      return $this;
    }

}
