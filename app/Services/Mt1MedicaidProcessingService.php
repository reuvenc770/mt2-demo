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

class Mt1MedicaidProcessingService extends Mt1FoodstampsProcessingService {
    protected $serviceName = 'Mt1MedicaidProcessingService';
    protected $logKeySuffix = '_realtime_medicaid';

    protected $realtimeDirectory = '/var/local/programdata/done/mt2_medicaid';
    protected $realtimeFeedId = 3018;

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
