<?php

namespace App\Jobs;
use App\Models\JobEntry;
use App\Jobs\Job;
use Cache;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
class AggregateAWeberSubscribers extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    use DispatchesJobs;

    CONST JOB_NAME = 'grabAWeberSubscribers - ';
    CONST KEY_NAME = "AWEBER_RATE_LIMIT_";
    protected $list;
    protected $tracking;
    protected $espAccountId;
    protected $nextPage;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($list, $tracking, $nextPage = null)
    {
        $this->list = $list;
        $this->tracking = $tracking;
        $this->espAccountId = $list->esp_account_id;
        $this->nextPage = $nextPage;
        JobTracking::startAggregationJob( self::JOB_NAME.$list->id , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    //Do I truncate the table before
    public function handle()
    {
        //TO Reduce Rate Limiting we are only letting one active thread per account
        if(Cache::get(self::KEY_NAME.$this->espAccountId) == 1){
            $this->release(0);
        };
        
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $time = Carbon::now()->addMinutes(120);

        Cache::put(self::KEY_NAME.$this->espAccountId,1,$time);//lock this account

        $subService = APIFactory::createApiSubscriptionService( "AWeber" , $this->espAccountId );
        if($this->nextPage) {
            $subscribers = $subService->getSinglePageSubscribers($this->nextPage);
        } else {
            $subscribers = $subService->getSinglePageSubscribers($this->list->subscribers_collection_link);
        }
        
        Cache::forget(self::KEY_NAME.$this->espAccountId);//give up the key

        foreach($subscribers['response']['entries'] as $subscriber){
         $subService->queueSubscriber($subscriber);
        }
        $subService->insertSubscribers();
        //if we have another next_collection_link we have more emails
        if(isset($subscribers['response']['next_collection_link'])){
            $job = (new AggregateAWeberSubscribers($this->list,str_random(16),$subscribers['response']['next_collection_link']))->onQueue("AWeber");
            $this->dispatch($job);
        }
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function failed()
    {
        Cache::forget(self::KEY_NAME.$this->espAccountId);
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
