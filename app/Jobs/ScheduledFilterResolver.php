<?php

namespace App\Jobs;

use App\Factories\ServiceFactory;
use App\Jobs\Job;
use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Services\AttributionRecordTruthService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maknz\Slack\Facades\Slack;
class ScheduledFilterResolver extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    public $filterName;
    public $date;

    public $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filterName, $date, $tracking)
    {
        $this->filterName = $filterName;
        $this->date = $date;
        $this->tracking = $tracking;
        JobTracking::startEspJob("Scheduled Filter {$filterName}","","",$this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AttributionRecordTruthService $truthService)
    {
        JobTracking::changeJobState( JobEntry::RUNNING , $this->tracking);
        $scheduledFilterService = ServiceFactory::createFilterService($this->filterName);
        $records = $scheduledFilterService->getRecordsByDate($this->date);
        $total = count($records);
        $columns = $scheduledFilterService->returnFullFields();

        foreach ($records->chunk(10000) as $chunkRecords){
            $emailIds = $chunkRecords->pluck("email_id")->all();
            foreach($columns as $key => $value){
                $truthService->bulkToggleFieldRecord($emailIds,$key,$value);
            }
        }
        JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking, $total);
    }

    public function failed()
    {
        JobTracking::changeJobState( JobEntry::FAILED , $this->tracking);

        Slack::to( self::SLACK_TARGET_SUBJECT )->send("Scheduled Filter for {$this->filterName} Failed to run after " . $this->attempts() . " attempts.");
    }
}
