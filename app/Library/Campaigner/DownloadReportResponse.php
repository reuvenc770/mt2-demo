<?php
namespace App\Library\Campaigner;
class DownloadReportResponse
{

    /**
     * @var ArrayOfReportResult $DownloadReportResult
     */
    protected $DownloadReportResult = null;

    /**
     * @param ArrayOfReportResult $DownloadReportResult
     */
    public function __construct($DownloadReportResult)
    {
      $this->DownloadReportResult = $DownloadReportResult;
    }

    /**
     * @return ArrayOfReportResult
     */
    public function getDownloadReportResult()
    {
      return $this->DownloadReportResult;
    }

    /**
     * @param ArrayOfReportResult $DownloadReportResult
     * @return DownloadReportResponse
     */
    public function setDownloadReportResult($DownloadReportResult)
    {
      $this->DownloadReportResult = $DownloadReportResult;
      return $this;
    }

}
