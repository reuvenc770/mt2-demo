<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\DataProcessingFactory;

/**
 * Class RetrieveReports
 * @package App\Jobs
 */
class PullCakeDeliverableStats extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "PullCakeDeliverableStats";
    protected $lookBack;
    protected $maxAttempts;
    protected $tracking;
    protected $source;

    public function __construct($source, $lookBack, $tracking) {
       $this->source = $source;
       $this->lookBack = $lookBack;
       $this->maxAttempts = env('MAX_ATTEMPTS',10);
       $this->tracking = $tracking;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        JobTracking::startAggregationJob(self::JOB_NAME . $this->source.$this->lookBack, $this->tracking);
        if ($this->attempts() > $this->maxAttempts) {
            $this->release(1);
        }

        $service = DataProcessingFactory::create(self::JOB_NAME);
        $service->run($this->lookBack);
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking, $this->attempts());

    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
    }
}