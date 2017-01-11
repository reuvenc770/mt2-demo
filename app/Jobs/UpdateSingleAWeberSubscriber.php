<?php

namespace App\Jobs;
use App\Models\JobEntry;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
class UpdateSingleAWeberSubscriber extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    CONST JOB_NAME = 'grabAWeberSubscribers';
    protected $subscriberLink;
    protected $tracking;
    protected $espAccountId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subscriberLink, $espAccountId, $tracking)
    {
        $this->subscriberLink = $subscriberLink;
        $this->tracking = $tracking;
        $this->espAccountId = $espAccountId;
        JobTracking::startAggregationJob( self::JOB_NAME , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    //Do I truncate the table before
    public function handle()
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $subService = APIFactory::createApiSubscriptionService( "AWeber" , $this->espAccountId );
        $subscriber = $subService->getSubscriber($this->subscriberLink);
        $subService->insertSubscriber($subscriber);
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
