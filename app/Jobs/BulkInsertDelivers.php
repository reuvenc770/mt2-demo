<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\ActionType;
use App\Repositories\EmailActionsRepo;
use App\Repositories\EmailRecordRepo;
use App\Repositories\RawDeliveredEmailRepo;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use App\Jobs\Traits\PreventJobOverlapping;

class BulkInsertDelivers extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;
    private $tracking;
    private $lookBack;
    private $name;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lookBack, $tracking){
        $this->name = "BulkInsertDelivers";
        $this->lookBack = $lookBack;
        $this->tracking = $tracking;
        JobTracking::startAggregationJob($this->name, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RawDeliveredEmailRepo $rawRepo, EmailRecordRepo $emailRecordRepo)
    {
        if ($this->jobCanRun($this->name)) {
            try {
                $this->createLock($this->name);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                echo "{$this->name} running" . PHP_EOL;
                
                $recordsToInsert = [];
                $grabbedRecords = $rawRepo->pullModelSince($this->lookBack);
                $grabbedRecords->chunk(10000, function($records) use ($emailRecordRepo) {
                    $boolRecordsHaveIds = true;
                    foreach($records as $record){
                        $recordsToInsert[] = [
                            'email' => $record->email_id,
                            'recordType' => "deliverable" ,
                            'espId' => $record->esp_account_id,
                            'deployId' => $record->deploy_id,
                            'espInternalId' => $record->esp_internal_id,
                            'date' => $record->datetime,
                        ];
                    }
                    $emailRecordRepo->massRecordDeliverables($recordsToInsert, $boolRecordsHaveIds);
                });
                
                $rawRepo->clearOutPast($this->lookBack + 5);
                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
            }
            catch (\Exception $e) {
                echo "{$this->name} failed with {$e->getMessage()}" . PHP_EOL;
                $this->failed();
            }
            finally {
                $this->unlock($this->name);
            }
        }
        else {
            echo "Still running {$this->name} - job level" . PHP_EOL;
            JobTracking::changeJobState(JobEntry::SKIPPED, $this->tracking);
        }
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED, $this->tracking);
    }
}
