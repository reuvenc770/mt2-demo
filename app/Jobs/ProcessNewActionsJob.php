<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\JobException;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Models\JobEntry;
use App\Facades\JobTracking;

use App\Services\NewActionsService;

class ProcessNewActionsJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    const BASE_JOB_NAME = 'ProcessNewActionsJob';

    protected $jobName;

    protected $dateRange;
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $dateRange , $tracking )
    {
        $this->dateRange = $dateRange;
        $this->tracking = $tracking;

        $this->jobName = self::BASE_JOB_NAME . ":" . json_encode( $dateRange );

        JobTracking::startAggregationJob( $this->jobName , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( NewActionsService $newActions )
    {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);

                JobTracking::changeJobState( JobEntry::RUNNING , $this->tracking );        

                $newActions->updateThirdPartyEmailStatuses( $this->dateRange );

                $newActions->updateAttributionRecordTruths( $this->dateRange );

                JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking );
            } catch ( \Exception $e ) {
                echo "{$this->jobName} failed with {$e->getMessage()} in file {$e->getFile()} on line {$e->getLine()}" . PHP_EOL;

                $this->failed();
            } finally {
                $this->unlock($this->jobName);
            }
        } else {
            echo "Still running {$this->jobName} - job level" . PHP_EOL;

            JobTracking::changeJobState( JobEntry::SKIPPED , $this->tracking );
        }
    }

    public function failed () {
        JobTracking::changeJobState( JobEntry::FAILED , $this->tracking );
    }
}
