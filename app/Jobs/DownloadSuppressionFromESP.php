<?php

namespace App\Jobs;

use App\Jobs\MonitoredJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
use App\Models\JobEntry;
use App\Facades\Suppression;

class DownloadSuppressionFromESP extends MonitoredJob implements ShouldQueue
{

    CONST JOB_NAME = "DownloadSuppressionFromESP";
    protected $tracking;
    protected $apiName;
    protected $espAccountId;
    protected $date;


    public function __construct($runtimeThreshold, $apiName, $espAccountId, $date, $tracking){

        $jobname = self::JOB_NAME."_".$apiName."_".$espAccountId;
        parent::__construct($jobname,$runtimeThreshold,$tracking);

        $this->apiName = $apiName;
        $this->espAccountId = $espAccountId;
        $this->date = $date;
        $this->tracking = $tracking;
    }

    public function handleJob()
    {
        $subscriptionService = APIFactory::createApiSubscriptionService($this->apiName,$this->espAccountId);
        $data = $subscriptionService->pullUnsubsEmailsByLookback($this->date);
        if($data){
            $subscriptionService->insertUnsubs($data, $this->espAccountId);
        }
        return count($data);
    }

}
