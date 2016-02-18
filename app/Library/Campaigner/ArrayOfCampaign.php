<?php
namespace App\Library\Campaigner;
class ArrayOfCampaign
{

    /**
     * @var Campaign[] $Campaign
     */
    protected $Campaign = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Campaign[]
     */
    public function getCampaign()
    {
      return $this->Campaign;
    }

    /**
     * @param Campaign[] $Campaign
     * @return ArrayOfCampaign
     */
    public function setCampaign(array $Campaign)
    {
      $this->Campaign = $Campaign;
      return $this;
    }

}
