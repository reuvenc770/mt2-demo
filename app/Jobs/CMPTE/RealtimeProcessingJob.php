<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs\CMPTE;

use App\Jobs\ProcessFeedRawFilesJob;
use App\Services\RemoteFeedFileService;

class RealtimeProcessingJob extends ProcessFeedRawFilesJob {
    protected $jobName = 'RealtimeProcessingJob-';

    public function __construct ( $tracking ) {
        parent::__construct( $tracking );
    }

    public function handle ( RemoteFeedFileService $service ) {
        $service = \App::make( \App\Services\CMPTE\RealtimeProcessingService::class );

        /**
         *
         * Uncomment this when we're ready for CMPTE 
         *
        $service->setFileProcessedCallback( function ( $file , $systemService , $meta ) {
            $systemService->deleteFile( $file[ 'path' ] );
        } );
         */

        parent::handle( $service );
    }
}
