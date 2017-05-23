<?php

namespace App\Jobs;

use App\Factories\ServiceFactory;
use App\Jobs\MonitoredJob;
use App\Models\JobEntry;
use App\Facades\JobTracking;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maknz\Slack\Facades\Slack;

class ScheduledFilterResolver extends MonitoredJob implements ShouldQueue
{
    public $filterName;
    public $date;

    public $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filterName, $date, $tracking, $runtime_threshold)
    {
        $jobname = "Scheduled Filter {$filterName}";

        parent::__construct($jobname,$runtime_threshold,$tracking);

        $this->filterName = $filterName;
        $this->date = $date;
        $this->tracking = $tracking;
        JobTracking::startEspJob($jobname,"","",$this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {
        $truthService = \App::make('\App\Services\AttributionRecordTruthService');

        $scheduledFilterService = ServiceFactory::createFilterService($this->filterName);
        
        $columns = $scheduledFilterService->returnFieldsForExpiration();
        $key = $scheduledFilterService->getSetFields()[0];
        $value = $columns[$key];

        $startPoint = $scheduledFilterService->getMinEmailIdForDate($this->date);
        $endPoint = $scheduledFilterService->getMaxEmailIdForDate($this->date);
        $total = 0;

        while ($startPoint < $endPoint) {
            $limit = 10000;
            $segmentEnd = $scheduledFilterService->nextNRows($startPoint, $limit);
            $segmentEnd = $segmentEnd ? $segmentEnd : $endPoint;

            $emails = $scheduledFilterService->getExpiringRecordsBetweenIds($this->date, $startPoint, $segmentEnd);

            if ($emails) {
                $total = $total + count($emails);
                $truthService->bulkToggleFieldRecord($emails, $key, $value);
            }
            
            $startPoint = $segmentEnd;
        }

        return $total;

    }
}
