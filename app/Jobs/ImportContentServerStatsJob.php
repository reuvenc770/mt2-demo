<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
use App\Models\JobEntry;

class ImportContentServerStatsJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    const JOB_NAME = "DownloadContentServerStats";
    private $tracking;
    private $start;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($start, $tracking) {
        $this->start = $start;
        $this->tracking = $tracking;
        JobTracking::startAggregationJob(self::JOB_NAME, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $service = APIFactory::createMt1DataImportService(self::JOB_NAME);
        $service->run($this->start);

        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
