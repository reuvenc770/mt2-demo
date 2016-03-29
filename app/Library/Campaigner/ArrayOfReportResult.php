<?php
namespace App\Library\Campaigner;
class ArrayOfReportResult
{

    /**
     * @var ReportResult[] $ReportResult
     */
    protected $ReportResult = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ReportResult[]
     */
    public function getReportResult()
    {
      return $this->ReportResult;
    }

    /**
     * @param ReportResult[] $ReportResult
     * @return ArrayOfReportResult
     */
    public function setReportResult(array $ReportResult)
    {
      $this->ReportResult = $ReportResult;
      return $this;
    }

}
