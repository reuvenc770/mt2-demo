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
use App\Models\MT1Models\User as Feeds;

class Mt1BatchProcessingService extends RemoteFeedFileService {
    protected $serviceName = 'Mt1BatchProcessingService';
    protected $slackChannel = '#cmp_hard_start_errors';
    protected $rootFileDirectory = '/home';
    protected $validDirectoryRegex = '/^\/(?:\w+)\/([a-zA-Z0-9_-]+)/';

    public function __construct (
        FeedService $feedService , 
        RemoteLinuxSystemService $systemService ,
        DomainGroupService $domainGroupService ,
        RawFeedEmailRepo $rawRepo
    ) {
        parent::__construct(
            $feedService ,
            $systemService ,
            $domainGroupService ,
            $rawRepo
        );
    }

    public function fireAlert ( $message ) {
        Slack::to( $this->slackChannel )->send( $message );
    }

    public function getFeedIdFromName ( $name ) {
        return ( $record = Feeds::where( 'username' , $name )->pluck( 'user_id' ) ) ? $record->pop() : null;
    }

    public function getValidFeedList () {
        return Feeds::where( [ [ 'status' , '=' , 'A'] , [ 'OrangeClient' , '=' , 'Y' ] ] )->pluck( 'username' )->toArray();
    }

    public function getRecentFiles ( $directory ) {
        $options = [ 
            '-type f' ,
            '-mtime -1' ,
            ' -not -path "/home/mt1/*"' ,
            "\( -name '*.csv' -o -name '*.txt' \)" ,
            '-print' 
        ]; 

        return $this->systemService->getRecentFiles( $directory , $options );
    }

    protected function connectToServer () {
        if ( !$this->systemService->connectionExists() ) { 
            $this->systemService->initSshConnection(
                config('ssh.servers.cmpte_feed_file_server.host'),
                config('ssh.servers.cmpte_feed_file_server.port'),
                config('ssh.servers.cmpte_feed_file_server.username'),
                config('ssh.servers.cmpte_feed_file_server.public_key'),
                config('ssh.servers.cmpte_feed_file_server.private_key')
            );  
        }   
    }

    protected function isCorrectDirectoryStructure ( $directory ) {
        return true;
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
        
        $record[ 'source_url' ] = ( strlen( $record[ 'source_url' ] ) > 255 ) ? substr( $record[ 'source_url' ] , 0 , 254 ) : $record[ 'source_url' ];


        if ( isset( $record[ 'dob' ] ) &&  $record[ 'dob' ] == '0000-00-00' ) {
            unset( $record[ 'dob' ] );
        } 

        return $record;
    }
}
