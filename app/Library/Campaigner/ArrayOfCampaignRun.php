<?php
namespace App\Library\Campaigner;
class ArrayOfCampaignRun
{

    /**
     * @var CampaignRun[] $CampaignRun
     */
    protected $CampaignRun = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return CampaignRun[]
     */
    public function getCampaignRun()
    {
      return $this->CampaignRun;
    }

    /**
     * @param CampaignRun[] $CampaignRun
     * @return ArrayOfCampaignRun
     */
    public function setCampaignRun(array $CampaignRun)
    {
      $this->CampaignRun = $CampaignRun;
      return $this;
    }

}
