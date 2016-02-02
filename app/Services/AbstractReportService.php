<?php

namespace App\Services;
use App\Services\Interfaces\IAPIReportService;
use App\Events\RawReportDataWasInserted;
use App\Repositories\ReportRepo;
use Illuminate\Support\Facades\Event;
use App\Services\API\EspBaseApi;

abstract class AbstractReportService implements IAPIReportService {
  
  protected $reportRepo;
  protected $api;

  public function __construct(ReportRepo $reportRepo, EspBaseApi $api) {
    $this->reportRepo = $reportRepo;
    $this->api = $api;
  }

  abstract public function retrieveApiReportStats($data);

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
}