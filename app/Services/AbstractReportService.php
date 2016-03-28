<?php

namespace App\Services;
use App\Events\RawReportDataWasInserted;
use App\Repositories\ReportRepo;
use Illuminate\Support\Facades\Event;
use App\Services\API\EspBaseApi;
use App\Services\Interfaces\IDataService;
use App\Services\EmailRecordService;

abstract class AbstractReportService implements IDataService  {
  const RECORD_TYPE_OPENER = 'opener';
  const RECORD_TYPE_CLICKER = 'clicker';
  const RECORD_TYPE_CONVERTER = 'converter';
  const RECORD_TYPE_DELIVERABLE = 'deliverable';
  const RECORD_TYPE_UNSUBSCRIBE = "unsubscribe";
  const RECORD_TYPE_COMPLAINT = "complaint";
  protected $reportRepo;
  protected $api;
  protected $emailRecord;

  public function __construct(ReportRepo $reportRepo, EspBaseApi $api , EmailRecordService $emailRecord ) {
    $this->reportRepo = $reportRepo;
    $this->api = $api;
    $this->emailRecord = $emailRecord;
  }

  abstract public function retrieveApiStats($data);

  abstract public function insertApiRawStats($data);

  abstract public function mapToRawReport($data);

  abstract public function mapToStandardReport($data);

  public function insertCsvRawStats($reports){
    $arrayReportList = array();
    foreach ($reports as $report) {
      $this->insertStats($this->api->getAccountName(), $report);
      $arrayReportList[] = $report;
    }

    Event::fire(new RawReportDataWasInserted($this, $arrayReportList));
  }

  protected function insertStats($accountName, $report) {
      try {
        $this->reportRepo->insertStats($accountName, $report);
      } catch (\Exception $e){
        throw new \Exception($e->getMessage());
      }
  }

  public function getCampaigns ( $accountName , $date ) {
      try {
        return $this->reportRepo->getCampaigns( $accountName , $date );
      } catch ( \Exception $e ) {
        throw new \Exception( $e->getMessage() );
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

  public function parseSubID($deploy_id){
    $return = isset(explode("_", $deploy_id)[0]) ? explode("_", $deploy_id)[0] : "";
    return $return;
  }

  protected function returnInfoForEmail($email) {

  }
}
