<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Traits\PreventJobOverlapping;
use Event;

class CommitAttributionJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    private $jobName = 'AttributionJob';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tracking) {
        //
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AttributionService $service) {

        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
                echo "{$this->jobName} running" . PHP_EOL;

                $records = $service->getTransientRecords();

                foreach ($records as $record) {
                    $beginDate = $record->capture_date;
                    $clientId = $record->client_id;
                    $currentAttributionLevel = (int)$record->level;
                    $actionDateTime = $record->action_datetime;
                    $hasAction = (bool)$record->has_action;
                    $actionExpired = $record->action_expired;

                    $potentialReplacements = $this->service->getPotentialReplacements($record->email_id);

                    foreach ($potentialReplacements as $repl) {
                        if ($this->changesAttribution($beginDate, , $repl->level)) {

                            $this->changeAttribution($record->email_id, $repl->client_id);

                            $beginDate = $repl->capture_date;
                            $currentAttrLevel = (int)$repl->level;
                            $hasAction = (bool)($repl->capture_date > $actionDateTime);
                        }
                    }
                }

                Event::fire(''); 
                // Want to fire off an event when attribution is complete
                JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);

            }
            catch (\Exception $e) {
                echo "{$this->jobName} failed with {$e->getMessage()}" . PHP_EOL;
                $this->failed();
            }
            finally {
                $this->unlock($this->jobName);
            }
        }

        else {
            echo "Still running {$this->jobName} - job level" . PHP_EOL;
            JobTracking::changeJobState(JobEntry::SKIPPED,$this->tracking);
        }

    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
        $this->unlock($this->jobName);
    }
}
