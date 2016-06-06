<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use App\Models\DeployRecordRerun;
use App\Repositories\DeployRecordRerunRepo;
use App\Models\EmailAction;
use App\Repositories\EmailActionsRepo;
use App\Services\CheckDeployService;

class CheckDeployStatsJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $lookback;
    private $tracking;
    const JOB_NAME = 'CheckDeployStats';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lookback, $tracking) {
        $this->lookback = $lookback;
        $this->tracking = $tracking;
        JobTracking::startAggregationJob(self::JOB_NAME, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        
        $actionsModel = new EmailAction();
        $actionsRepo = new EmailActionsRepo($actionsModel);
        $rerunModel = new DeployRecordRerun();
        $rerunRepo = new DeployRecordRerunRepo($rerunModel);


        $service = new CheckDeployService($actionsRepo, $rerunRepo, $this->tracking);
        $service->run();
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
