<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Repositories\DeployRepo;
use App\Repositories\ListProfileCombineRepo;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Services\ListProfileExportService;
use Cache;
class ExportListProfileJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;
    const BASE_NAME = 'ListProfileExport-';
    private $jobName;
    private $listProfileId;
    private $offerId;
    private $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($listProfileId, $offerId, $tracking) {
        $this->listProfileId = $listProfileId;
        $this->offerId = $offerId;
        $this->tracking = $tracking;

        $this->jobName = self::BASE_NAME . $listProfileId . ':' . $offerId;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ListProfileExportService $service, DeployRepo $deployRepo) {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                $offer = $this->offerId ? $this->offerId : array();

                //If its being used for a deploy lets build up some Deploy Cache
                if($this->offerId >= 1){
                    $deploys = $deployRepo->getDeploysFromProfileAndOffer($this->listProfileId,$this->offerId);
                    $service->exportListProfileToMany($this->listProfileId, $offer,$deploys);
                } else {
                    $service->exportListProfile($this->listProfileId, $offer);
                }
                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
            }
            catch (\Exception $e) {
                echo "{$this->jobName} failed with {$e->getMessage()}  {$e->getLine()}" . PHP_EOL;
                $this->failed();
            }
            finally {
                $this->unlock($this->jobName);
            }
        }
        else {
            echo "Still running {$this->jobName} - job level" . PHP_EOL;
            JobTracking::changeJobState(JobEntry::SKIPPED, $this->tracking);
        }
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED, $this->tracking);
    }
}
