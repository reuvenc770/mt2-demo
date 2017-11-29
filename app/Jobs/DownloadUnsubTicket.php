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
    protected $unsubData;
    private $hbData;
    protected $maxAttempts;

    public function __construct($apiName, $espAccountId, $unsubData, $hbData, $tracking){

        $jobname = self::JOB_NAME."_".$apiName."_".$espAccountId;
        parent::__construct($jobname,null,$tracking);

        $this->apiName = $apiName;
        $this->espAccountId = $espAccountId;
        $this->unsubData = $unsubData;
        $this->hbData = $hbData;
        $this->maxAttempts = config('jobs.maxAttempts');
        $this->tracking = $tracking;
    }

    public function handleJob()
    {
        JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
        $subscriptionService = APIFactory::createApiSubscriptionService($this->apiName,$this->espAccountId);
        $unsubs = $subscriptionService->getUnsubReport($this->unsubData['ticketId'], $this->unsubData['count']);
        $hbs = $subscriptionService->getUnsubReport($this->hbData['ticketId'], $this->hbData['count']);

        if($unsubs){
            $subscriptionService->insertUnsubs($unsubs);
        } 

        if ($hbs) {
            $subscriptionService->insertHardBounces($hbs);
        }

        if (!$unsubs && !$hbs) {
            $this->release(60);
        }
        return count($unsubs) + count($hbs);
    }

}
