<?php
namespace App\Library\Campaigner;
class SetCampaignRecipients
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var int $campaignId
     */
    protected $campaignId = null;

    /**
     * @var CampaignRecipientsData $campaignRecipients
     */
    protected $campaignRecipients = null;

    /**
     * @param Authentication $authentication
     * @param int $campaignId
     * @param CampaignRecipientsData $campaignRecipients
     */
    public function __construct($authentication, $campaignId, $campaignRecipients)
    {
      $this->authentication = $authentication;
      $this->campaignId = $campaignId;
      $this->campaignRecipients = $campaignRecipients;
    }

    /**
     * @return Authentication
     */
    public function getAuthentication()
    {
      return $this->authentication;
    }

    /**
     * @param Authentication $authentication
     * @return SetCampaignRecipients
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return int
     */
    public function getCampaignId()
    {
      return $this->campaignId;
    }

    /**
     * @param int $campaignId
     * @return SetCampaignRecipients
     */
    public function setCampaignId($campaignId)
    {
      $this->campaignId = $campaignId;
      return $this;
    }

    /**
     * @return CampaignRecipientsData
     */
    public function getCampaignRecipients()
    {
      return $this->campaignRecipients;
    }

    /**
     * @param CampaignRecipientsData $campaignRecipients
     * @return SetCampaignRecipients
     */
    public function setCampaignRecipients($campaignRecipients)
    {
      $this->campaignRecipients = $campaignRecipients;
      return $this;
    }

}
