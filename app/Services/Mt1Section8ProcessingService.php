<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Services\Mt1FoodstampsProcessingService;
use App\Services\FeedService;
use App\Services\DomainGroupService;
use App\Services\RemoteLinuxSystemService;
use App\Repositories\RawFeedEmailRepo;

class Mt1Section8ProcessingService extends Mt1FoodstampsProcessingService {
    protected $serviceName = 'Mt1Section8ProcessingService';
    protected $logKeySuffix = '_realtime_section8';

    protected $realtimeDirectory = '/var/local/programdata/done/mt2_hosting';
    protected $realtimeFeedId = 2961;

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
}
