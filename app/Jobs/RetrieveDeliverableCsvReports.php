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



  protected $filePath;
  protected $accountId;
  protected $tracking;
  protected $action;
  protected $espName;
  protected $maxAttempts;
  protected $actionTableRepo;

  public function __construct($espName, $accountId, $action, $filePath, $tracking) {
    $this->accountId = $accountId;
    $this->filePath = $filePath;
    $this->tracking = $tracking;
    $this->action = $action;
    $this->espName = $espName;

    $this->maxAttempts = 1;
    
  }

  public function handle() {
    JobTracking::startEspJob(self::JOB_NAME, $this->espName, $this->accountId, $this->tracking);

    $reportService = APIFactory::createCsvDeliverableService($this->espName);
    $reportArray = $reportService->setCsvToFormat($this->accountId, $this->action, $this->filePath);
    $reportService->insertDeliverableCsvActions($reportArray);
    JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking, 1); // Do we really need attempts?
  }

  public function failed() {
    JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
  }


}