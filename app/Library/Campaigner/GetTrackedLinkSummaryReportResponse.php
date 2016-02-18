<?php
namespace App\Library\Campaigner;
class GetTrackedLinkSummaryReportResponse
{

    /**
     * @var ArrayOfTrackedLinkSummaryData $GetTrackedLinkSummaryReportResult
     */
    protected $GetTrackedLinkSummaryReportResult = null;

    /**
     * @param ArrayOfTrackedLinkSummaryData $GetTrackedLinkSummaryReportResult
     */
    public function __construct($GetTrackedLinkSummaryReportResult)
    {
      $this->GetTrackedLinkSummaryReportResult = $GetTrackedLinkSummaryReportResult;
    }

    /**
     * @return ArrayOfTrackedLinkSummaryData
     */
    public function getGetTrackedLinkSummaryReportResult()
    {
      return $this->GetTrackedLinkSummaryReportResult;
    }

    /**
     * @param ArrayOfTrackedLinkSummaryData $GetTrackedLinkSummaryReportResult
     * @return GetTrackedLinkSummaryReportResponse
     */
    public function setGetTrackedLinkSummaryReportResult($GetTrackedLinkSummaryReportResult)
    {
      $this->GetTrackedLinkSummaryReportResult = $GetTrackedLinkSummaryReportResult;
      return $this;
    }

}
