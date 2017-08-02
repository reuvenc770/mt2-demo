<?php
/**
 * @author Adam Chin <achin@zetagloba.com>
 */

namespace App\Jobs;

use App\Jobs\ProcessFeedRawFilesJob;

class ProcessMt1RealtimeFeedFilesJob extends ProcessFeedRawFilesJob {
    protected $jobName = 'ProcessMt1RealtimeFeedFilesJob-';
    protected $serviceName = '\\App\\Services\\Mt1RealtimeProcessingService';
    protected $folderName = 'mt2_realtime';
    protected $archiveDir = '/var/local/programdata/done/mt2_realtime_archive/';

    public function __contruct ( $tracking , $runtimeThreshold="15m" ) {
        parent::__construct( $tracking , $runtimeThreshold );
    }

    protected function getService () {
        $service = \App::make( $this->serviceName );
        $folderName = $this->folderName;
        $archiveDir = $this->archiveDir;

        $service->setFileProcessedCallback( function ( $files , $systemService , $meta ) use ( $folderName , $archiveDir ) {
            foreach ( $files as $file ) {
                $newPath = null;

                if ( strpos( $file[ 'path' ] , $folderName ) !== false ) {
                    $newPath = $archiveDir . basename( $file[ 'path' ] );
                }

                if ( !is_null( $newPath ) ) {
                    $output = $systemService->moveFile( $file[ 'path' ] , $newPath );
                }
            }
        } );

        return $service;
    }
}
