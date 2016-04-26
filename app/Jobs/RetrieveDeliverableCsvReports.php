<?php

namespace App\Jobs;

use App;
use App\Factories\APIFactory;
use App\Repositories\EmailRecordRepo;
use App\Services\EmailRecordService;
use DaveJamesMiller\Breadcrumbs\Exception;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\JobEntry;
use App\Facades\EspApiAccount;
use App\Facades\JobTracking;


class RetrieveDeliverableCsvReports extends Job implements ShouldQueue {

  use InteractsWithQueue, SerializesModels;
  const JOB_NAME = "RetrieveDeliverableCsvReports";

  protected $espAccountName;
  protected $date;
  protected $filePath;
  protected $tracking;
  protected $action;
  protected $maxAttempts;

  public function __construct($espAccountName, $file, $realDate, $action, $tracking) {

    $this->espAccountName = $espAccountName;
    $this->filePath = $file;
    $this->maxAttempts = env('MAX_ATTEMPTS',10);
    $this->tracking = $tracking;
    $this->date = $realDate;
    $this->action = $action;
    
  }

  public function handle() {
    $espAccountDetails = EspApiAccount::getEspAccountDetailsByName($this->espAccountName);
    JobTracking::startEspJob(self::JOB_NAME, $espAccountDetails->esp->name, $espAccountDetails->id, $this->tracking);

    $reportService = APIFactory::createCsvDeliverableService($espAccountDetails->id, $espAccountDetails->esp->name);

    $reportArray = $reportService->setCsvToFormat($this->filePath);
    $emailRecordService = App::make("App\\Services\\EmailRecordService");

    foreach ($reportArray as $record){
      $emailRecordService->queueDeliverable(
          $this->action,
          $record['email_address'] ,
          $espAccountDetails->id ,
          $record['deploy_id'] ,
          $record['esp_internal_id'],
          $record['datetime']);
    }
    $emailRecordService->massRecordDeliverables();
    JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking, 1); // Do we really need attempts?

    /* 
    move/delete here - after the job so the move doesn't cause problems/duplication
    looks like:
    $s3 = Storage::disk('s3');
    $s3->put('/archived-deliverable-csvs/' . $this->filePath, fopen($this->filePath))
    */
  }

  public function failed() {
    JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
  }


}