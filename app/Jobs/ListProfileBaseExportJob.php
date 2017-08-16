<?php

namespace App\Jobs;

use App\Facades\JobTracking;
use App\Services\ListProfileService;
use App\Services\ListProfileScheduleService;
use App\Jobs\ExportDeployCombineJob;
use Carbon\Carbon;
use App\DataModels\CacheReportCard;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Cache;
use DB;

class ListProfileBaseExportJob extends MonitoredJob {
    use DispatchesJobs;

    protected $tracking;
    private $profileId;
    protected $jobName;
    protected $params;
    private $cacheTagName;

    public function __construct($profileId, $cacheTagName, $tracking, $runtimeThreshold=null, $params=null) {
        $this->profileId = $profileId;
        $this->tracking = $tracking;
        $this->jobName = 'ListProfileGeneration-' . $profileId;
        $this->cacheTagName = $cacheTagName;
        $this->params = $params;

        parent::__construct($this->jobName,$runtimeThreshold,$tracking);

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

        if(isset($this->params['test-connection-only'])){
           $this->testConnection();
           return 0;
        }

        $lpCount = $service->buildProfileTable($this->profileId);
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
                    $username = $deploy->user ? $deploy->user->username : 'no_user';
                    $reportCard = CacheReportCard::makeNewReportCard("{$username}-{$deploy->id}-{$runId}");
                    $this->dispatch(new ExportDeployCombineJob([$deploy], $reportCard, str_random(16),$this->runtimeSecondsThreshold));
                }
 
            }
        }

        return $lpCount;
    }

    private function testConnection(){
        try {
            DB::connection('redshift')->getPdo();
            JobTracking::addDiagnostic(array('notices' => 'Redshift DB connection test: success'),$this->tracking);
            return 1;
        } catch (\Exception $e) {
            JobTracking::addDiagnostic(array('errors' => 'Redshift DB connection test: failed'),$this->tracking);
            throw new \Exception("Could not connect to the Redshift DB");
            return;
        }
    }
}
