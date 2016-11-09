<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\ListProfileCombineService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Services\ListProfileExportService;
use Storage;
class ExportListProfileCombineJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;
    const BASE_NAME = 'ListCombineExport-';
    private $jobName;
    private $listCombineId;
    private $offerId;
    private $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($listCombineId, $offerId, $tracking) {
        $this->listProfileId = $listCombineId;
        $this->offerId = $offerId;
        $this->tracking = $tracking;

        $this->jobName = self::BASE_NAME . $listCombineId . ':' . $offerId;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ListProfileExportService $service, ListProfileCombineService $combineService) {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                $combine = $combineService->getCombineById($this->listCombineId);
                $combineFile = "ListProfiles/{$combine->name}.csv";

                foreach($combine->listProfiles as $listProfile) {
                    $fileName = $service->export($listProfile->id, $this->offerId);
                    $contents = Storage::get($fileName);
                    Storage::append($combineFile, $contents);
                }
                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
            }
            catch (\Exception $e) {
                echo "{$this->jobName} failed with {$e->getMessage()}" . PHP_EOL;
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
        $this->unlock($this->jobName);
    }
}
