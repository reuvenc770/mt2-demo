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
    private $maxAttempts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tracking) {
        $this->tracking = $tracking;
        $this->maxAttempts = env('MAX_ATTEMPTS', 3);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        JobTracking::startAggregationJob(self::JOB_NAME, $this->tracking);

        $service = APIFactory::createMt1DataImportService(self::JOB_NAME);
        $service->run();

        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking, $this->attempts());
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
    }
}
