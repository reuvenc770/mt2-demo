<?php
namespace App\Library\Campaigner;
class SocialSharingSettings
{

    /**
     * @var boolean $AllowSocialCampaign
     */
    protected $AllowSocialCampaign = null;

    /**
     * @var string $ButtonText
     */
    protected $ButtonText = null;

    /**
     * @var int $FormId
     */
    protected $FormId = null;

    /**
     * @param boolean $AllowSocialCampaign
     * @param string $ButtonText
     */
    public function __construct($AllowSocialCampaign, $ButtonText)
    {
      $this->AllowSocialCampaign = $AllowSocialCampaign;
      $this->ButtonText = $ButtonText;
    }

    /**
     * @return boolean
     */
    public function getAllowSocialCampaign()
    {
      return $this->AllowSocialCampaign;
    }

    /**
     * @param boolean $AllowSocialCampaign
     * @return SocialSharingSettings
     */
    public function setAllowSocialCampaign($AllowSocialCampaign)
    {
      $this->AllowSocialCampaign = $AllowSocialCampaign;
      return $this;
    }

    /**
     * @return string
     */
    public function getButtonText()
    {
      return $this->ButtonText;
    }

    /**
     * @param string $ButtonText
     * @return SocialSharingSettings
     */
    public function setButtonText($ButtonText)
    {
      $this->ButtonText = $ButtonText;
      return $this;
    }

    /**
     * @return int
     */
    public function getFormId()
    {
      return $this->FormId;
    }

    /**
     * @param int $FormId
     * @return SocialSharingSettings
     */
    public function setFormId($FormId)
    {
      $this->FormId = $FormId;
      return $this;
    }

}
