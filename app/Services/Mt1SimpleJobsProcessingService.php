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

class Mt1SimpleJobsProcessingService extends Mt1FoodstampsProcessingService {
    protected $serviceName = 'Mt1SimpleJobsProcessingService';
    protected $logKeySuffix = '_realtime_simplejobs';

    protected $realtimeDirectory = '/var/local/programdata/done/mt2_simplyjobs';
    protected $realtimeFeedId = 2983;
    protected $realtimeFileColumnMap = [
        'city' ,
        'email_address' ,
        'first_name' ,
        'last_name' ,
        'state' ,
        'source_url' ,
        'zip' ,
        'ip' ,
        'ts' ,
        'job_type' ,
        'gradyear' ,
        'salary' ,
        'educationlevel' ,
        'utm_source' ,
        'password' ,
        'sourceID' ,
        'capture_date' ,
    ];

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
