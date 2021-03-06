<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Services\RemoteFeedFileService;
use Maknz\Slack\Facades\Slack;
use App\Services\FeedService;
use App\Services\DomainGroupService;
use App\Services\RemoteLinuxSystemService;
use App\Models\ProcessedFeedFile;
use App\Repositories\RawFeedEmailRepo;
use Illuminate\Support\Facades\Redis;

class ReprocessBatchProcessingService extends RemoteFeedFileService {
    protected $serviceName = 'ReprocessBatchProcessingService';
    protected $slackChannel = '#cmp_hard_start_errors';

    public function __construct ( FeedService $feedService , RemoteLinuxSystemService $systemService , DomainGroupService $domainGroupService , RawFeedEmailRepo $rawRepo ) {
        parent::__construct( $feedService , $systemService , $domainGroupService , $rawRepo );
    }

    public function setCreds ( $hostConfig , $portConfig , $userConfig , $publicKeyConfig , $privateKeyConfig ) {
        $this->hostConfig = $hostConfig;
        $this->portConfig = $portConfig;
        $this->userConfig = $userConfig;
        $this->publicKeyConfig = $publicKeyConfig;
        $this->privateKeyConfig = $privateKeyConfig;

        $this->connectToServer();
    }

    public function setFile ( $filePath , $feedId , $party ) {
        $this->newFileList[] = [
            'path' => trim( $filePath ) ,
            'feedId' => $feedId ,
            'party' => $party 
        ];
    }

    public function fireAlert ( $message ) {
        Slack::to( $this->slackChannel )->send( $message );
    }

    public function loadNewFilePaths () {}

    protected function lockExists () { return false; }

    protected function connectToServer () {
        if ( !$this->systemService->connectionExists() ) { 
            $this->systemService->initSshConnection(
                config( $this->hostConfig ),
                config( $this->portConfig ),
                config( $this->userConfig ),
                config( $this->publicKeyConfig ),
                config( $this->privateKeyConfig )
            );  
        }   
    }
    
    protected function columnMatchCheck ( $lineColumns ) {
        #columns have a chance to not match. parsing is overridden in this class
    }

    protected function mapRecord ( $lineColumns ) {
        $record = [];
        
        if ( count( $this->currentColumnMap ) <= 0 || !is_array( $this->currentColumnMap ) ) {
            \Log::error( 'Column mapping failed due to invalid mapping for file: ' . $this->currentFile[ 'path' ] );
            return null;
        }

        foreach ( $this->currentColumnMap as $index => $columnName ) {
            if ( isset( $lineColumns[ $index ] ) ) {
                $record[ $columnName ] = $lineColumns[ $index ];
            } else {
                $record[ $columnName ] = '';
            }
        }

        $record[ 'feed_id' ] = $this->currentFile[ 'feedId' ];
        $record[ 'party' ] = $this->currentFile[ 'party' ];
        $record[ 'realtime' ] = 0;
        $record[ 'file' ] = $this->currentFile[ 'path' ];

        if ( !isset( $record[ 'source_url' ] ) || $record[ 'source_url' ] == '' ) {
            $record[ 'source_url' ] = $this->feedService->getSourceUrlFromId( $record[ 'feed_id' ] );
        }

        if ( isset( $record[ 'dob' ] ) &&  $record[ 'dob' ] == '0000-00-00' ) {
            unset( $record[ 'dob' ] );
        } 

        return $record;
    }
}
