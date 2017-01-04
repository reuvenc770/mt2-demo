<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
use App\Models\JobEntry;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DownloadUnsubTicket extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "DownloadSuppressionFromESP-DownloadTicketSave";
    protected $tracking;
    protected $apiName;
    protected $espAccountId;
    protected $data;
    protected $maxAttempts;

    public function __construct($apiName, $espAccountId, $data, $tracking){
        $this->apiName = $apiName;
        $this->espAccountId = $espAccountId;
        $this->data = $data;
        $this->maxAttempts = config('jobs.maxAttempts');
        $this->tracking = $tracking;
    }

    public function handle()
    {
        JobTracking::startEspJob(self::JOB_NAME,$this->apiName, $this->espAccountId, $this->tracking);
        JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
        $subscriptionService = APIFactory::createApiSubscriptionService($this->apiName,$this->espAccountId);
        $data = $subscriptionService->getUnsubReport($this->data['ticketId'], $this->data['count']);

        if($data){
            $subscriptionService->insertUnsubs($data);
        } else {
            $this->release(60);
        }
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }


    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
