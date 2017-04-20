<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\ActionType;
use App\Repositories\EmailActionsRepo;
use App\Repositories\RawDeliveredEmailRepo;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use App\Jobs\Traits\PreventJobOverlapping;
use Carbon\Carbon;

class BulkInsertDelivers extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;
    private $tracking;
    private $lookBack;
    private $name;
    const SEGMENT_SIZE = 10000;
    
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
    public function handle(RawDeliveredEmailRepo $rawRepo, EmailActionsRepo $actionsRepo)
    {
        if ($this->jobCanRun($this->name)) {
            try {
                $this->createLock($this->name);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                echo "{$this->name} running" . PHP_EOL;

                $date = Carbon::today()->subDays($this->lookBack)->toDateTimeString();
                $minId = $rawRepo->getMinIdForDate($date);
                $maxId = $rawRepo->getMaxIdForDate($date);

                while($minId <= $maxId) {
                    $segmentEnd = $minId + self::SEGMENT_SIZE;

                    $records = $rawRepo->getSegmentBetweenIds($minId, $segmentEnd);
                    $recordsToInsert = [];

                    foreach($records as $row) {
                        $recordsToInsert[] = '(' . $row->email_id .','. $row->deploy_id .','. $row->esp_account_id .','. $row->esp_internal_id .',4,"'. $row->datetime .'", now())';
                    }

                    $actionsRepo->upsertDelivered($recordsToInsert);
                    $minId = $segmentEnd;
                }

                $rawRepo->clearOutPast($this->lookBack + 5);

                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
            }
            catch (\Exception $e) {
                echo "{$this->name} failed with {$e->getMessage()}" . PHP_EOL;
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
