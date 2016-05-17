<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
use App\Models\JobEntry;
use App\Facades\Suppression;
class DownloadSuppressionFromESP extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "DownloadSuppressionFromESP";
    protected $tracking;
    protected $apiName;
    protected $espAccountId;
    protected $date;


    public function __construct($apiName, $espAccountId, $date, $tracking){
        $this->apiName = $apiName;
        $this->espAccountId = $espAccountId;
        $this->date = $date;
        $this->tracking = $tracking;
        JobTracking::startEspJob(self::JOB_NAME,$this->apiName, $this->espAccountId, $this->tracking);
    }

    public function handle()
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $subscriptionService = APIFactory::createApiSubscriptionService($this->apiName,$this->espAccountId);
        $data = $subscriptionService->pullUnsubsEmailsByLookback($this->date);
        if($data){
                $subscriptionService->insertUnsubs($data, $this->espAccountId);
        }
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }


    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
