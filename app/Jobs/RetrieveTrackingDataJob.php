<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;

/**
 * Class RetrieveReports
 * @package App\Jobs
 */
class RetrieveTrackingDataJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "RetrieveTrackingApiData-";

    CONST PROCESS_TYPE_AGGREGATE = 1;
    CONST PROCESS_TYPE_RECORD = 2;

    protected $startDate;
    protected $endDate;

    protected $tracking;
    protected $source;

    protected $isRecordProcessing = false;

    public function __construct($source, $startDate, $endDate, $tracking , $processType = RetrieveTrackingDataJob::PROCESS_TYPE_AGGREGATE ) {
       $this->source = $source;
       $this->startDate = $startDate;
       $this->endDate = $endDate;
       $this->tracking = $tracking;

       if ( $processType === RetrieveTrackingDataJob::PROCESS_TYPE_RECORD ) {
          $this->isRecordProcessing = true;
       }

       $fullJobName = self::JOB_NAME . $this->source . '-' . ( $processType == self::PROCESS_TYPE_AGGREGATE ? 'aggregate-' : 'record-' );

       JobTracking::startTrackingJob( $fullJobName ,$this->startDate, $this->endDate, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);

        $dataService= APIFactory::createTrackingApiService($this->source, $this->startDate,$this->endDate);

        $apiRequestData = null;

        if ( $this->isRecordProcessing ) {
            $apiRequestData = [ "recordstats" => 1 ];
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
