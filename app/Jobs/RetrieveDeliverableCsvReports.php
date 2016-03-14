<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Factories\APIFactory;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\EspApiAccount;
use App\Models\JobEntry;
use App\Facades\JobTracking;

class RetrieveDeliverableCsvReports extends Job implements ShouldQueue {

  use InteractsWithQueue, SerializesModels;
  const JOB_NAME = "RetrieveDeliverableCsvReports";

  protected $filePath;
  protected $accountName;
  protected $tracking;
  protected $action;
  protected $espName;
  protected $maxAttempts;

  public function __construct($espName, $accountName, $action, $filePath, $tracking) {
    $this->accountName = $accountName;
    $this->filePath = $filePath;
    $this->tracking = $tracking;
    $this->action = $action;
    $this->espName = $espName;

    $this->maxAttempts = 1;
  }

  public function handle() {
    JobTracking::startEspJob(self::JOB_NAME, $this->espName, $this->accountName, $this->tracking);
    $reportService = APIFactory::createApiReportService($this->espName, $this->accountName, $this->tracking);
    $reportArray = EspApiAccount::mapCsvToRawActionsArray($this->accountName, $this->action, $this->filePath);
    $reportService->insertDeliverableCsvActions($reportArray, $this->filePath);
    JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking, 1); // Do we really need attempts?
  }

  public function failed() {
    JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
  }
}