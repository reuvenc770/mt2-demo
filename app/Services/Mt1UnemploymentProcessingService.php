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

class Mt1UnemploymentProcessingService extends Mt1FoodstampsProcessingService {
    protected $serviceName = 'Mt1UnemploymentProcessingService';
    protected $logKeySuffix = '_realtime_unemployment';

    protected $realtimeDirectory = '/var/local/programdata/done/mt2_unemployment';
    protected $realtimeFeedId = 3017;

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
