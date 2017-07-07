<?php

namespace App\Jobs;
use App\Models\JobEntry;
use App\Jobs\Job;
use App\Services\AWeberListService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
class AWeberUpdateLists extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "AWeberUpdateLists";
    protected $tracking;
    protected $espAccountId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($espAccount, $tracking)
    {
        $this->tracking = $tracking;
        $this->espAccountId = $espAccount;
        JobTracking::startAggregationJob( self::JOB_NAME , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AWeberListService $listService)
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $reportService = APIFactory::createAPIReportService( "AWeber" , $this->espAccountId );
        $lists = $reportService->getMailingLists();
        foreach($lists as $list){
            $listService->updateOrAddList($list,$this->espAccountId);
        }
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }
    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
