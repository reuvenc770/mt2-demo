<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Jobs\Traits\PreventJobOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Services\EmailFeedInstanceService;

class UpdateFeedCountJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    protected $jobName = 'UpdateFeedCountJob';
    protected $startDate;
    protected $endDate;
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $startDate , $endDate , $tracking )
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->tracking = $tracking;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( EmailFeedInstanceService $service )
    {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                $totalCount = $service->updateSourceUrlCounts( $this->startDate , $this->endDate );
                JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking,$totalCount);
            }
            catch (\Exception $e) {
                echo "{$this->jobName} failed with {$e->getMessage()}  {$e->getLine()}" . PHP_EOL;
                $this->failed();
            }
            finally {
                $this->unlock($this->jobName);
            }
        } else {
            echo "Still running {$this->jobName} - job level" . PHP_EOL;
            JobTracking::changeJobState(JobEntry::SKIPPED, $this->tracking);
        }
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
