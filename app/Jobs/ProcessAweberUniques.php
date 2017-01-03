<?php

namespace App\Jobs;

use App\Exceptions\JobException;
use App\Jobs\Job;
use App\Models\AWeberReport;
use App\Models\JobEntry;
use App\Factories\APIFactory;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Facades\JobTracking;
use Log;
class ProcessAweberUniques extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;
    
    protected $jobName = 'ProcessAweberUniques';
    protected $tracking;
    protected $id;
    protected $espAccountId;
    protected $type;
    protected $infoUrl;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $espAccoiuntId, $url, $type, $tracking)
    {

        $this->tracking = $tracking;
        $this->infoUrl = $url;
        $this->id = $id;
        $this->espAccountId = $espAccoiuntId;
        $this->type = $type;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->jobCanRun($this->jobName)) {
            $reportService = APIFactory::createAPIReportService("AWeber" , $this->espAccountId);
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                switch ($this->type){
                    case AWeberReport::UNIQUE_OPENS:
                        $value = $reportService->getUniqueStatForCampaignUrl($this->infoUrl,AWeberReport::UNIQUE_OPENS);
                        $reportService->updateUniqueStatForCampaignUrl($this->id, $this->type, $value);
                        break;
                    case AWeberReport::UNIQUE_CLICKS:
                        $value = $reportService->getUniqueStatForCampaignUrl($this->infoUrl,AWeberReport::UNIQUE_CLICKS);
                        $reportService->updateUniqueStatForCampaignUrl($this->id, $this->type, $value);
                        break;
                    default:
                        throw new JobException("Not a valid action type");
                }
                JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
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
