<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs\CMPTE;

use App\Jobs\ProcessFeedRawFilesJob;
use App\Services\RemoteFeedFileService;

class BatchProcessingJob extends ProcessFeedRawFilesJob {
    protected $jobName = 'BatchProcessingJob-';

    public function __construct ( $tracking ) {
        parent::__construct( $tracking );
    }

    public function handle( RemoteFeedFileService $service ) {
        $service = \App::make( \App\Services\CMPTE\BatchProcessingService::class );

        $service->setFileProcessedCallback( function ( $file , $systemService , $meta ) {
            $newPath = str_replace( '/mt1' , '' , $file[ 'path' ] );
            $output = $systemService->moveFile( $file[ 'path' ] , $newPath );

            \Log::info( $output );
        } );

        parent::handle( $service );
    }
}
