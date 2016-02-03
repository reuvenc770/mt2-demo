<?php

namespace App\Services;
use App\Events\RawReportDataWasInserted;
use App\Repositories\ReportRepo;
use Illuminate\Support\Facades\Event;
use App\Services\API\EspBaseApi;
use App\Services\Interfaces\IDataService;

abstract class AbstractReportService implements IDataService  {
  
  protected $reportRepo;
  protected $api;

  public function __construct(ReportRepo $reportRepo, EspBaseApi $api) {
    $this->reportRepo = $reportRepo;
    $this->api = $api;
  }

  abstract public function retrieveApiStats($data);

  abstract public function insertApiRawStats($data);

  abstract public function mapToRawReport($data);

  abstract public function mapToStandardReport($data);

  public function insertCsvRawStats($reports){
    $arrayReportList = array();
    foreach ($reports as $report) {
      $this->insertStats($this->getAccountName(), $report);
      $arrayReportList[] = $report;
    }

    Event::fire(new RawReportDataWasInserted($this->getApiName(),$this->getAccountName(), $arrayReportList));
  }

  protected function insertStats($accountName, $report) {
      try {
        $this->reportRepo->insertStats($accountName, $report);
      } catch (\Exception $e){
        throw new \Exception($e->getMessage());
      }
  }

  public function insertSegmentedApiRawStats($data, $length) {
    $start = 0;
    $end = 5000;

    while ($end < $length) {
      echo "Inserting segment ..." . PHP_EOL;
      $slice = array_slice($data, $start, $end);
      $this->insertApiRawStats($slice);
      $start = $end;
      $end = $end + 5000;
    } 
  }
}