<?php

namespace App\Jobs;
use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Factories\ServiceFactory;
use Cache;
use Mail;

class S3RedshiftExportJob extends MonitoredJob {

    const TALLY_KEY = 'ListProfileReadiness';

    protected $tracking;
    private $entity;
    protected $jobName;
    private $version;
    private $extraData;

    public function __construct($entity, $version, $tracking, $extraData = null, $runtimeThreshold=null) {
        if ($version < -1 || $version > 2) {
            throw new \Exception("Job run type must be -1, 0, 1, or 2. Currently is $version.");
        }

        if (2 === $version && null === $extraData) {
            throw new \Exception("Special run for $entity must have specific run data. Have null.");
        }

        if (null !== $extraData && $version !== 2) {
            throw new \Exception("Extra specifying data passed in for standard job.");
        }

        $this->entity = $entity;
        $this->jobName = $entity . '-s3';
        $this->tracking = $tracking;
        $this->version = $version;
        $this->extraData = $extraData;

        $this->updateNotificationTally( $entity );

        parent::__construct($this->jobName,$runtimeThreshold,$this->tracking);

        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handleJob() {

                $service = ServiceFactory::createAwsExportService($this->entity);
                $rows = 0;

                if(-1 == $this->version){
                    JobTracking::addDiagnostic(array('notices' => 'checking connection only'),$this->tracking);
                }
                elseif (1 === $this->version) {
                    $rows = $service->extractAll();
                    $service->loadAll();
                }
                elseif (2 === $this->version) {
                    // Not available from command line.
                    $rows = $service->specialExtract($this->extraData);
                    $service->loadAll();
                }
                else {
                    $rows = $service->extract();
                    $service->load();
                }

                self::updateNotificationTally( $this->entity , false );
                return $rows;
    }

    static public function clearNotificationTally () {
        Cache::forget( self::TALLY_KEY );
    }

    protected function updateNotificationTally ( $entity , $increment = true ) {
        $notificationEntities = [ 'EmailFeedAssignment' , 'ListProfileFlatTable' , 'RecordData' ];

        if ( !in_array( $entity , $notificationEntities ) ) {
            return false;
        }

        if ( $increment ) {
            Cache::increment( self::TALLY_KEY );

            return true;
        }

        Cache::decrement( self::TALLY_KEY );

        $allJobsCompleted = ( 0 === (int)Cache::get( self::TALLY_KEY ) );
        if ( $allJobsCompleted ) {
            Mail::raw( 'List Profile Preprocessing Finished.' , function ($message) {
                $message->to( 'GTDDev@zetaglobal.com' );
                $message->to( 'orangeac@zetaglobal.com' );

                $message->subject('"List Profile Readiness"');
                $message->priority(1);
            });
        }

        return true;
    }
}
