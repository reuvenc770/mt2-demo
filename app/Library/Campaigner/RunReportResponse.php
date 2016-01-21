<?php
namespace App\Library\Campaigner;
class RunReportResponse
{

    /**
     * @var ReportTicket $RunReportResult
     */
    protected $RunReportResult = null;

    /**
     * @param ReportTicket $RunReportResult
     */
    public function __construct($RunReportResult)
    {
      $this->RunReportResult = $RunReportResult;
    }

    /**
     * @return ReportTicket
     */
    public function getRunReportResult()
    {
      return $this->RunReportResult;
    }

    /**
     * @param ReportTicket $RunReportResult
     * @return RunReportResponse
     */
    public function setRunReportResult($RunReportResult)
    {
      $this->RunReportResult = $RunReportResult;
      return $this;
    }

}
