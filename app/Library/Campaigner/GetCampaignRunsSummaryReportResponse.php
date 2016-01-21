<?php
namespace App\Library\Campaigner;
class GetCampaignRunsSummaryReportResponse
{

    /**
     * @var ArrayOfCampaign $GetCampaignRunsSummaryReportResult
     */
    protected $GetCampaignRunsSummaryReportResult = null;

    /**
     * @param ArrayOfCampaign $GetCampaignRunsSummaryReportResult
     */
    public function __construct($GetCampaignRunsSummaryReportResult)
    {
      $this->GetCampaignRunsSummaryReportResult = $GetCampaignRunsSummaryReportResult;
    }

    /**
     * @return ArrayOfCampaign
     */
    public function getGetCampaignRunsSummaryReportResult()
    {
      return $this->GetCampaignRunsSummaryReportResult;
    }

    /**
     * @param ArrayOfCampaign $GetCampaignRunsSummaryReportResult
     * @return GetCampaignRunsSummaryReportResponse
     */
    public function setGetCampaignRunsSummaryReportResult($GetCampaignRunsSummaryReportResult)
    {
      $this->GetCampaignRunsSummaryReportResult = $GetCampaignRunsSummaryReportResult;
      return $this;
    }

}
