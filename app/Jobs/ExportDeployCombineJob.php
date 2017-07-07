<?php

namespace App\Jobs;

use App\Facades\JobTracking;
use App\Models\JobEntry;
use App\DataModels\ReportEntry;
use App\DataModels\CacheReportCard;

class ExportDeployCombineJob extends MonitoredJob {
    const BASE_NAME = 'ExportDeployCombine-';
    protected $jobName;
    private $deploys;
    private $reportCard;
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $deploys, CacheReportCard $reportCard, $tracking, $runtimeThreshold) {
        $this->deploys = $deploys;
        $this->reportCard = $reportCard;
        $this->tracking = $tracking;
        $deployIds = [];

        foreach($deploys as $d) {
            $deployIds[] = $d->id;
        }

        $deployNames = implode(',', $deployIds);
        $this->jobName = self::BASE_NAME . $deployNames;

        parent::__construct($this->jobName,$runtimeThreshold,$tracking);

        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob() {

        $service = \App::make('\App\Services\ListProfileExportService');


                foreach ($this->deploys as $deploy) {
                    $entry = new ReportEntry($deploy->name);
                    $entry = $service->createDeployExport($deploy, $entry);
                    $this->reportCard->addEntry($entry);
                }

                $this->reportCard->mail();

    }
}
