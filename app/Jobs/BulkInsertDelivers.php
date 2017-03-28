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
class BulkInsertDelivers extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $tracking;
    private $lookBack;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lookBack, $tracking){
        $name = "BulkInsertDelivers";
        $this->lookBack = $lookBack;
        $this->tracking = $tracking;
        JobTracking::startAggregationJob($name, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RawDeliveredEmailRepo $rawRepo, EmailRecordRepo $emailRecordRepo)
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
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
        $rawRepo->clearOutPast($this->lookBack);
        JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
