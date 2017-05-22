<?php

namespace App\Jobs;

use App\Jobs\MonitoredJob;
use Illuminate\Queue\SerializesModels;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
use App\Models\JobEntry;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DownloadUnsubTicket extends MonitoredJob implements ShouldQueue
{
    CONST JOB_NAME = "DownloadSuppressionFromESP-DownloadTicketSave";
    protected $tracking;
    protected $apiName;
    protected $espAccountId;
    protected $data;
    protected $maxAttempts;
    protected $runtime_threshold='6m';

    public function __construct($apiName, $espAccountId, $data, $tracking){

        $jobname = self::JOB_NAME."_".$apiName."_".$espAccountId;
        parent::__construct($jobname,$this->runtime_threshold,$tracking);

        $this->apiName = $apiName;
        $this->espAccountId = $espAccountId;
        $this->data = $data;
        $this->maxAttempts = config('jobs.maxAttempts');
        $this->tracking = $tracking;
        JobTracking::startEspJob($jobname ,$this->apiName, $this->espAccountId, $this->tracking);
    }

    public function handleJob()
    {
        JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
        $subscriptionService = APIFactory::createApiSubscriptionService($this->apiName,$this->espAccountId);
        $data = $subscriptionService->getUnsubReport($this->data['ticketId'], $this->data['count']);

        if($data){
                $subscriptionService->insertUnsubs($data);
        } else {
            $this->release(60);
        }
        return count($data);
    }

}
