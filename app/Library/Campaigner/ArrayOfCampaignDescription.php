<?php
namespace App\Library\Campaigner;
class ArrayOfCampaignDescription
{

    /**
     * @var CampaignDescription[] $CampaignDescription
     */
    protected $CampaignDescription = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return CampaignDescription[]
     */
    public function getCampaignDescription()
    {
      return $this->CampaignDescription;
    }

    /**
     * @param CampaignDescription[] $CampaignDescription
     * @return ArrayOfCampaignDescription
     */
    public function setCampaignDescription(array $CampaignDescription)
    {
      $this->CampaignDescription = $CampaignDescription;
      return $this;
    }

}
