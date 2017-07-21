<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs\CMPTE;

use App\Jobs\ProcessFeedRawFilesJob;
use App\Services\RemoteFeedFileService;

class RealtimeProcessingJob extends ProcessFeedRawFilesJob {
    const ARCHIVE_DIR_REALTIME = '/var/local/programdata/done/mt2_realtime_archive/';
    const FOLDER_NAME_REALTIME = 'mt2_realtime';

    const ARCHIVE_DIR_UNEMPLOY = '/var/local/programdata/done/mt2_unemployment_archive/';
    const FOLDER_NAME_UNEMPLOY = 'mt2_unemployment';

    const ARCHIVE_DIR_MEDICAID = '/var/local/programdata/done/mt2_medicaid_archive/';
    const FOLDER_NAME_MEDICAID = 'mt2_medicaid';

    const ARCHIVE_DIR_HOSTING = '/var/local/programdata/done/mt2_hosting_archive/';
    const FOLDER_NAME_HOSTING = 'mt2_hosting';

    const ARCHIVE_DIR_FDSTAMP = '/var/local/programdata/done/mt2_foodstamps_archive/';
    const FOLDER_NAME_FDSTAMP = 'mt2_foodstamps';

    protected $jobName = 'RealtimeProcessingJob-';

    public function __construct ( $tracking ) {
        parent::__construct( $tracking );
    }

    public function handle ( RemoteFeedFileService $service ) {
        $service = \App::make( \App\Services\CMPTE\RealtimeProcessingService::class );

        $pathMap = $this->getPathMap();

        $service->setFileProcessedCallback( function ( $files , $systemService , $meta ) use ( $pathMap ) {
            foreach ( $files as $file ) {
                $newPath = null;

                foreach ( $pathMap as $folderName => $archiveDir ) {
                    if ( strpos( $file[ 'path' ] , $folderName ) !== false ) {
                        $newPath = $archiveDir . basename( $file[ 'path' ] );
                        break;
                    }
                }

                if ( !is_null( $newPath ) ) {
                    $output = $systemService->moveFile( $file[ 'path' ] , $newPath );
                }
            }
        } );

        parent::handle( $service );
    }

    protected function getPathMap () {
        return [
            self::FOLDER_NAME_REALTIME => self::ARCHIVE_DIR_REALTIME ,
            self::FOLDER_NAME_UNEMPLOY => self::ARCHIVE_DIR_UNEMPLOY , 
            self::FOLDER_NAME_MEDICAID => self::ARCHIVE_DIR_MEDICAID ,
            self::FOLDER_NAME_HOSTING => self::ARCHIVE_DIR_HOSTING ,
            self::FOLDER_NAME_FDSTAMP => self::ARCHIVE_DIR_FDSTAMP
        ];
    }
}
