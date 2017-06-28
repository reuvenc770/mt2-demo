<?php

namespace App\Services;

use Carbon\Carbon;
use App\Repositories\AttributionRecordTruthRepo;
use App\Repositories\EmailFeedAssignmentRepo;
use Cache;
use Log;

use Artisan;

class AttributionService
{
    private $truthRepo;
    private $emailRepo;
    private $assignmentRepo;
    
    private $name = 'AttributionJob';
    const LIMIT = 65000;

    public function __construct(AttributionRecordTruthRepo $truthRepo, 
                                EmailFeedAssignmentRepo $assignmentRepo) {

        $this->truthRepo = $truthRepo;
        $this->assignmentRepo = $assignmentRepo;
    }

    public function run($argObj, $remainder) {
        $type = $argObj['type'];

        if ('feedInvalidation' === $type) {
            $feedId = $argObj['feedId'];
            $startPoint = $this->assignmentRepo->maxEmailIdForFeed($feedId);
            $endPoint = $this->assignmentRepo->minEmailIdForFeed($feedId);
        }
        else {
            $startPoint = $this->truthRepo->maxId();
            $endPoint = $this->truthRepo->minId();
        }

        while ($startPoint < $endPoint) {        
            if ('feedInvalidation' === $type) {            
                //We need to get all feed instances and reassign if possible.
                $segmentEnd = $this->truthRepo->nextNRowsForAttribution($feedId, $startPoint, self::LIMIT) ?: $endPoint;
                $records = $this->truthRepo->getFeedAttributionsBetweenIds($feedId, $remainder, $startPoint, $endPoint);
            }
            elseif ('model' === $type || $lastAttrLevelChange->gte($carbonDate)) {
                /* 
                    If a model is specified, or if attribution has changed recently,
                    we need to pick up all available transients. This is distinct from
                    rerunning *all* records because it does not need to trawl the entire database -
                    only two cases in the attribution flow chart.
                    There *are* some cases that this will miss in the case of level change:
                    something *would have* changed had the levels been different at some point in the past.
                    However, this omission is deliberate - attribution only moves forward (except for
                    the case above).
                */
                $segmentEnd = $this->truthRepo->nextNRows($startPoint, self::LIMIT) ?: $endPoint;
                $records = $this->truthRepo->getFullTransientsBetweenIds($remainder, $startPoint, $endPoint);
            }
            else {
                // Otherwise, run the optimized subset
                $segmentEnd = $this->truthRepo->nextNRows($startPoint, self::LIMIT) ?: $endPoint;
                $datetime = $carbonDate->toDateTimeString();
                $records = $this->truthRepo->getOptimizedTransientsBetweenIds($datetime, $remainder, $startPoint, $endPoint);
            }
        
            if ($records) {
                Artisan::call('attribution:processBatch', [
                    'data' => $records
                ]);
            
                Cache::increment($this->name);
            }
        
            $startPoint = $segmentEnd;
        }
    }

}
