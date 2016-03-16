<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Factories\APIFactory;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\JobEntry;
use App\Facades\JobTracking;


class RetrieveDeliverableCsvReports extends Job implements ShouldQueue {

  use InteractsWithQueue, SerializesModels;
  const JOB_NAME = "RetrieveDeliverableCsvReports";

  private $espId;
  private $filePath;
  private $accountId;
  private $tracking;
  private $action;
  private $espName;
  private $maxAttempts;
  private $actionTableRepo;

  public function __construct($espId, $espName, $accountId, $action, $filePath, $tracking) {
    $this->espId = $espId;
    $this->accountId = $accountId;
    $this->filePath = $filePath;
    $this->tracking = $tracking;
    $this->action = $action;
    $this->espName = $espName;

    $this->maxAttempts = 1;
    
  }

  public function handle() {
    JobTracking::startEspJob(self::JOB_NAME, $this->espName, $this->accountId, $this->tracking);

    $reportService = APIFactory::createCsvDeliverableService($this->espId, $this->espName);
    $reportArray = $reportService->setCsvToFormat($this->accountId, $this->action, $this->filePath);
    $reportService->insertDeliverableCsvActions($reportArray);
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