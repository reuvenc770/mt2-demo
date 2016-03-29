<?php
namespace App\Library\Campaigner;
class TrackedLinkDescription
{

    /**
     * @var int $Id
     */
    protected $Id = null;

    /**
     * @var int $CampaignId
     */
    protected $CampaignId = null;

    /**
     * @var string $Name
     */
    protected $Name = null;

    /**
     * @var string $Url
     */
    protected $Url = null;

    /**
     * @var TrackedLinkType $LinkType
     */
    protected $LinkType = null;

    /**
     * @param int $Id
     * @param int $CampaignId
     * @param string $Name
     * @param string $Url
     * @param TrackedLinkType $LinkType
     */
    public function __construct($Id, $CampaignId, $Name, $Url, $LinkType)
    {
      $this->Id = $Id;
      $this->CampaignId = $CampaignId;
      $this->Name = $Name;
      $this->Url = $Url;
      $this->LinkType = $LinkType;
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
     * @return TrackedLinkDescription
     */
    public function setId($Id)
    {
      $this->Id = $Id;
      return $this;
    }

    /**
     * @return int
     */
    public function getCampaignId()
    {
      return $this->CampaignId;
    }

    /**
     * @param int $CampaignId
     * @return TrackedLinkDescription
     */
    public function setCampaignId($CampaignId)
    {
      $this->CampaignId = $CampaignId;
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
     * @return TrackedLinkDescription
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
      return $this->Url;
    }

    /**
     * @param string $Url
     * @return TrackedLinkDescription
     */
    public function setUrl($Url)
    {
      $this->Url = $Url;
      return $this;
    }

    /**
     * @return TrackedLinkType
     */
    public function getLinkType()
    {
      return $this->LinkType;
    }

    /**
     * @param TrackedLinkType $LinkType
     * @return TrackedLinkDescription
     */
    public function setLinkType($LinkType)
    {
      $this->LinkType = $LinkType;
      return $this;
    }

}
