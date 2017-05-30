<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services\CMPTE;

use App\Services\RemoteFeedFileService;
use Maknz\Slack\Facades\Slack;
use App\Services\FeedService;
use App\Services\DomainGroupService;
use App\Services\RemoteLinuxSystemService;
use App\Models\ProcessedFeedFile;
use App\Repositories\RawFeedEmailRepo;
use App\Models\MT1Models\User as Feeds;

class BatchProcessingService extends RemoteFeedFileService {
    protected $slackChannel = '#cmp_hard_start_errors';
    protected $rootFileDirectory = '/home';
    protected $validDirectoryRegex = '/^\/(?:\w+)\/([a-zA-Z0-9_-]+)/';

    public function __construct ( FeedService $feedService , RemoteLinuxSystemService $systemService , DomainGroupService $domainGroupService , RawFeedEmailRepo $rawRepo ) {
        parent::__construct( $feedService , $systemService , $domainGroupService , $rawRepo );
    }

    public function fireAlert ( $message ) {
        Slack::to( $this->slackChannel )->send( $message );
    }

    public function getFeedIdFromName ( $name ) {
        return ( $record = Feeds::where( 'username' , $name )->pluck( 'user_id' ) ) ? $record->pop() : null;
    }

    public function getValidFeedList () {
        return Feeds::where( 'status' , 'A' )->pluck( 'username' )->toArray();
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
}
