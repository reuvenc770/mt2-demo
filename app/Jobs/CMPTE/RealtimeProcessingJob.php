<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs\CMPTE;

use App\Jobs\ProcessFeedRawFilesJob;
use App\Services\RemoteFeedFileService;

class RealtimeProcessingJob extends ProcessFeedRawFilesJob {
    const ARCHIVE_DIR = '/var/local/programdata/done/mt2_realtime_archive/';
    protected $jobName = 'RealtimeProcessingJob-';

    public function __construct ( $tracking ) {
        parent::__construct( $tracking );
    }

    public function handle ( RemoteFeedFileService $service ) {
        $service = \App::make( \App\Services\CMPTE\RealtimeProcessingService::class );

        $service->setFileProcessedCallback( function ( $file , $systemService , $meta ) {
            #only do this for normal realtime since there are so many more records and seems like mt1 overwrites these files.
            if ( strpos( $file[ 'path' ] , 'mt2_realtime' ) !== false ) {
                $newPath = self::ARCHIVE_DIR . basename( $file[ 'path' ] );
                $output = $systemService->moveFile( $file[ 'path' ] , $newPath );
            }
        } );

        parent::handle( $service );
    }
}
