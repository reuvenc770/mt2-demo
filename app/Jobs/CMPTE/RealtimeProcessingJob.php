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

        $service->setFileProcessedCallback( function ( $file , $systemService , $meta ) {
            \Log::info( $meta );
        } );

        parent::handle( $service );
    }
}
