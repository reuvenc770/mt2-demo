<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\ProcessFeedRawFilesJob;

class ProcessMt1BatchFeedFilesJob extends ProcessFeedRawFilesJob {
    protected $jobName = 'ProcessMt1BatchFeedFilesJob-';

    public function __construct ( $tracking , $runtimeThreshold="15m" ) {
        parent::__construct( $tracking , $runtimeThreshold );
    }

    protected function getService () {
        $service = \App::make( \App\Services\Mt1BatchProcessingService::class );

        $service->setFileProcessedCallback( function ( $files , $systemService , $meta ) {
            foreach ( $files as $file ) {
                $newPath = '/home/mt1' . str_replace( '/home' , '' , $file[ 'path' ] );
                $output = $systemService->moveFile( $file[ 'path' ] , $newPath );
            }
        } );

        return $service;
    }
}
