<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
use App\Repositories\CakeAffiliateRepo;

/**
 * Class RetrieveReports
 * @package App\Jobs
 */
class RetrieveTrackingDataJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "RetrieveTrackingApiData-";

    CONST PROCESS_TYPE_ACTION = 1;
    CONST PROCESS_TYPE_RECORD = 2;

    protected $startDate;
    protected $endDate;

    protected $tracking;
    protected $source;

    protected $isRecordProcessing = false;

    public function __construct($source, $startDate, $endDate, $tracking, $processType = RetrieveTrackingDataJob::PROCESS_TYPE_ACTION ) {
       $this->source = $source;
       $this->startDate = $startDate;
       $this->endDate = $endDate;
       $this->tracking = $tracking;

       if ( $processType === RetrieveTrackingDataJob::PROCESS_TYPE_RECORD ) {
          $this->isRecordProcessing = true;
       }

       $fullJobName = self::JOB_NAME . $this->source . '-' . ( $processType == self::PROCESS_TYPE_ACTION ? 'action-' : 'record-' );

       JobTracking::startTrackingJob($fullJobName, $this->startDate, $this->endDate, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CakeAffiliateRepo $affRepo)
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $dataService = APIFactory::createTrackingApiService($this->source, $this->startDate,$this->endDate);

        $cakeIds = array_map(function($obj) { return $obj['id']; }, $affRepo->getAll()->toArray());
        $apiRequestData = ['cakeids' => implode(',', $cakeIds)];

        if ( $this->isRecordProcessing ) {
            $apiRequestData["recordstats"] = 1;
        }
        else {
            $apiRequestData['actions'] = 1;
        }

        $stats = $dataService->retrieveApiStats( $apiRequestData );
        $dataLength = sizeof( $stats );

        if ( !empty( $stats ) && $dataLength < 10000 ) {
            $dataService->insertApiRawStats( $stats , $this->isRecordProcessing );
        } elseif ( !empty( $stats ) && $dataLength > 10000 ) {
            $dataService->insertSegmentedApiRawStats(
                $stats ,
                $dataLength ,
                $this->isRecordProcessing
            );
        }

        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }


    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
