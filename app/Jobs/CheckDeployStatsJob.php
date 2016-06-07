<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use App\Factories\DataProcessingFactory;

class CheckDeployStatsJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $lookback;
    private $tracking;
    const JOB_NAME = 'CheckDeployStats';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lookback, $tracking) {
        $this->lookback = $lookback;
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
        $service = DataProcessingFactory::create(self::JOB_NAME);
        $service->run();
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
