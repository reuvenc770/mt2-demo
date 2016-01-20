<?php
namespace App\Library\Campaigner;
class CampaignFilter
{

    /**
     * @var ArrayOfInt $CampaignIds
     */
    protected $CampaignIds = null;

    /**
     * @var ArrayOfInt $CampaignRunIds
     */
    protected $CampaignRunIds = null;

    /**
     * @var ArrayOfString $CampaignNames
     */
    protected $CampaignNames = null;

    /**
     * @param ArrayOfInt $CampaignIds
     * @param ArrayOfInt $CampaignRunIds
     * @param ArrayOfString $CampaignNames
     */
    public function __construct($CampaignIds, $CampaignRunIds, $CampaignNames)
    {
      $this->CampaignIds = $CampaignIds;
      $this->CampaignRunIds = $CampaignRunIds;
      $this->CampaignNames = $CampaignNames;
    }

    /**
     * @return ArrayOfInt
     */
    public function getCampaignIds()
    {
      return $this->CampaignIds;
    }

    /**
     * @param ArrayOfInt $CampaignIds
     * @return CampaignFilter
     */
    public function setCampaignIds($CampaignIds)
    {
      $this->CampaignIds = $CampaignIds;
      return $this;
    }

    /**
     * @return ArrayOfInt
     */
    public function getCampaignRunIds()
    {
      return $this->CampaignRunIds;
    }

    /**
     * @param ArrayOfInt $CampaignRunIds
     * @return CampaignFilter
     */
    public function setCampaignRunIds($CampaignRunIds)
    {
      $this->CampaignRunIds = $CampaignRunIds;
      return $this;
    }

    /**
     * @return ArrayOfString
     */
    public function getCampaignNames()
    {
      return $this->CampaignNames;
    }

    /**
     * @param ArrayOfString $CampaignNames
     * @return CampaignFilter
     */
    public function setCampaignNames($CampaignNames)
    {
      $this->CampaignNames = $CampaignNames;
      return $this;
    }

}
