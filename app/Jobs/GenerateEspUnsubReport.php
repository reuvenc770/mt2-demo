<?php

namespace App\Jobs;

use App\Reports\SuppressionExportReport;

class GenerateEspUnsubReport extends MonitoredJob
{

    protected $date;
    protected $tracking;
    protected $jobName;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date, $tracking, $runtimeThreshold)
    {
        $this->date = $date;
        $this->tracking = $tracking;
        $this->jobName = "GenerateEspUnsubReport";

        parent::__construct($this->jobName, $runtimeThreshold, $tracking);

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob() {
        $exportReport = \App::make(\App\Reports\SuppressionExportReport::class);
        $exportReport->run($this->date);
    }
}
