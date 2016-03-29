<?php
namespace App\Library\Campaigner;
class SendTestCampaign
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
     * @var ContactKey $contactKeyForTest
     */
    protected $contactKeyForTest = null;

    /**
     * @var ArrayOfString $emails
     */
    protected $emails = null;

    /**
     * @param Authentication $authentication
     * @param int $campaignId
     * @param ContactKey $contactKeyForTest
     * @param ArrayOfString $emails
     */
    public function __construct($authentication, $campaignId, $contactKeyForTest, $emails)
    {
      $this->authentication = $authentication;
      $this->campaignId = $campaignId;
      $this->contactKeyForTest = $contactKeyForTest;
      $this->emails = $emails;
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
     * @return SendTestCampaign
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
     * @return SendTestCampaign
     */
    public function setCampaignId($campaignId)
    {
      $this->campaignId = $campaignId;
      return $this;
    }

    /**
     * @return ContactKey
     */
    public function getContactKeyForTest()
    {
      return $this->contactKeyForTest;
    }

    /**
     * @param ContactKey $contactKeyForTest
     * @return SendTestCampaign
     */
    public function setContactKeyForTest($contactKeyForTest)
    {
      $this->contactKeyForTest = $contactKeyForTest;
      return $this;
    }

    /**
     * @return ArrayOfString
     */
    public function getEmails()
    {
      return $this->emails;
    }

    /**
     * @param ArrayOfString $emails
     * @return SendTestCampaign
     */
    public function setEmails($emails)
    {
      $this->emails = $emails;
      return $this;
    }

}
