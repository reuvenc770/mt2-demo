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
    protected $maxAttempts;

    public function __construct($apiName, $espAccountId, $date, $tracking){
        $this->apiName = $apiName;
        $this->espAccountId = $espAccountId;
        $this->date = $date;
        $this->maxAttempts = env('MAX_ATTEMPTS',10);
        $this->tracking = $tracking;
    }

    public function handle()
    {
        JobTracking::startEspJob(self::JOB_NAME,$this->apiName, $this->espAccountId, $this->tracking);

        $subscriptionService = APIFactory::createApiSubscriptionService($this->apiName,$this->espAccountId);
        $data = $subscriptionService->pullUnsubsEmailsByLookback($this->date); //Realized that the ESP should get rid of rows not job.
        if($data){
            // likely needs to be changed

            foreach ($data as $entry) {

                $data = $subscriptionService->mapToSuppressionTable($entry, $this->espAccountId);
                if($campaign_id == 0){// System Opt Out
                    continue;
                }

                Suppression::recordRawUnsub($data);
            }
        }

        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking, $this->attempts());
    }


    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
    }
}
