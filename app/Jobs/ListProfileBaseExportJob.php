<?php

namespace App\Jobs;

use App\Facades\JobTracking;
use App\Services\ListProfileService;
use App\Services\ListProfileScheduleService;
use App\Jobs\ExportDeployCombineJob;
use Carbon\Carbon;
use App\DataModels\CacheReportCard;
use Cache;

class ListProfileBaseExportJob extends MonitoredJob {

    protected $tracking;
    private $profileId;
    protected $jobName;
    private $cacheTagName;

    public function __construct($profileId, $cacheTagName, $tracking, $runtime_threshold) {
        $this->profileId = $profileId;
        $this->tracking = $tracking;
        $this->jobName = 'ListProfileGeneration-' . $profileId;
        $this->cacheTagName = $cacheTagName;

        parent::__construct($this->jobName,$runtime_threshold,$tracking);

        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Build a table for each list profile against each offer it used in.
     * 0 for offer is passed for scheduled list exports
     * @param ListProfileService $service
     * @param ListProfileScheduleService $schedule
     */
    public function handleJob() {

        $service = \App::make('\App\Services\ListProfileService');
        $schedule = \App::make('\App\Services\ListProfileScheduleService');
        $deployRepo = \App::make('\App\Repositories\DeployRepo');

        $service->buildProfileTable($this->profileId);
        $this->dispatch(new ExportListProfileJob($this->profileId, str_random(16)));
        $schedule->updateSuccess($this->profileId); // These might not just be scheduled ...

        if (null !== $this->cacheTagName) {
            // make this optional
            Cache::decrement($this->cacheTagName, 1);

            if ((int)Cache::get($this->cacheTagName) <= 0) {
                $date = Carbon::today()->toDateString();
                $deploys = $deployRepo->getDeploysForToday($date);

                foreach($deploys as $deploy) {
                    $runId = str_random(10);
                    $reportCard = CacheReportCard::makeNewReportCard("{$deploy->user->username}-{$deploy->id}-{$runId}");
                    $this->dispatch(new ExportDeployCombineJob([$deploy], $reportCard, str_random(16),$this->runtime_threshold));
                }
            }
        }


    }
}