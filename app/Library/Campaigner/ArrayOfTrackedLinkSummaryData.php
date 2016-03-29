<?php
namespace App\Library\Campaigner;
class ArrayOfTrackedLinkSummaryData
{

    /**
     * @var TrackedLinkSummaryData[] $TrackedLinkSummaryData
     */
    protected $TrackedLinkSummaryData = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return TrackedLinkSummaryData[]
     */
    public function getTrackedLinkSummaryData()
    {
      return $this->TrackedLinkSummaryData;
    }

    /**
     * @param TrackedLinkSummaryData[] $TrackedLinkSummaryData
     * @return ArrayOfTrackedLinkSummaryData
     */
    public function setTrackedLinkSummaryData(array $TrackedLinkSummaryData)
    {
      $this->TrackedLinkSummaryData = $TrackedLinkSummaryData;
      return $this;
    }

}
