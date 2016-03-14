<?php

namespace App\Services;
use App\Events\RawReportDataWasInserted;
use App\Repositories\ReportRepo;
use Illuminate\Support\Facades\Event;
use App\Services\API\EspBaseApi;
use App\Services\Interfaces\IDataService;

class AbstractCsvDeliverableService {

  protected $emailRecordRepo;
  protected $actionTableRepo;

  public function __construct($actionTableRepo, $emailRecordRepo) {
    $this->deliverableTableRepo = $actionTableRepo;
    $this->emailRecordRepo = $emailRecordRepo;

  }


  public function insertDeliverableCsvActions($data, $filePath) {
    foreach ($data as $row) {
        /**
         Given just an email address. We need:
            - email_id >> get from email
            - client_id >> need to get from attribution
            - esp_id >> can get from API
            - action_id >> can get from the action type
            - date, time, datetime >> might need to set a default
            - campaign_id >> probably can get from filename in this case


         */
      }
  }
  
}