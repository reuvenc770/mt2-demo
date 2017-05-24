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

class BatchProcessingService extends RemoteFeedFileService {
    protected $slackChannel = '#cmp_hard_start_errors';
    protected $rootFileDirectory = '/home/mt1';
    protected $validDirectoryRegex = '/^\/(?:\w+)\/mt1\/([a-zA-Z0-9_-]+)/';

    public function __construct ( FeedService $feedService , RemoteLinuxSystemService $systemService , DomainGroupService $domainGroupService , RawFeedEmailRepo $rawRepo ) {
        parent::__construct( $feedService , $systemService , $domainGroupService , $rawRepo );
    }

    public function fireAlert ( $message ) {
        Slack::to( $this->slackChannel )->send( $message );
    }

    public function getFeedIdFromName ( $name ) {
        #uses full name
        
        return parent::getFeedIdFromName( $name ); #temporary for testing. switch out for full names
    }

    public function getValidFeedList () {
        #grab full names of active feeds
        
        return parent::getValidFeedList(); #temporary for testing. switch out for full names
    }
}
