<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Traits\PreventJobOverlapping;

use App\Models\JobEntry;
use App\Facades\JobTracking;

use App\Services\RemoteFeedFileService;

class ProcessFeedRawFilesJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    protected $jobName = 'ProcessFeedRawFilesJob-';
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $tracking )
    {
        $this->tracking = $tracking;

        $this->jobName .= $tracking;

        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( RemoteFeedFileService $service )
    {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);

                JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);

                $service->processNewFiles();

                JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
            } catch ( \Exception $e ) {
                echo "{$this->jobName} failed with {$e->getMessage()}" . PHP_EOL;

                \Log::error( $e );

                $this->failed();
            } finally {
                $this->unlock($this->jobName);
            }
        } else {
            echo "Still running {$this->jobName} - job level" . PHP_EOL;

            JobTracking::changeJobState(JobEntry::SKIPPED, $this->tracking);
        }
    }

    public function failed () {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
