<?php

namespace App\Jobs;

use App\Factories\ReportFactory;

class ExportActionsJob extends MonitoredJob
{

    protected $reportName;
    protected $date;
    protected $tracking;
    protected $jobName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($reportName, $date, $tracking, $runtimeThreshold) {
        $this->reportName = $reportName;
        $this->date = $date;
        $this->tracking = $tracking;
        $this->jobName = $reportName . '-' . $date;
        parent::__construct($this->jobName,$runtimeThreshold,$tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob() {
        $exportReportService = ReportFactory::createReport($this->reportName);
        $exportReportService->execute($this->date);
        $exportReportService->notify();
    }
}
