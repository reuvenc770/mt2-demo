<?php

namespace App\Services;

use Carbon\Carbon;
use App\Repositories\AttributionRecordTruthRepo;
use App\Repositories\AttributionLevelRepo;
use App\Repositories\EtlPickupRepo;
use Cache;
use Log;

use Artisan;

class AttributionService
{
    private $truthRepo;
    private $pickupRepo;
    private $levelRepo;
    
    private $name = 'AttributionJob';

    public function __construct(AttributionRecordTruthRepo $truthRepo, 
                                AttributionLevelRepo $levelRepo,
                                EtlPickupRepo $pickupRepo) {

        $this->truthRepo = $truthRepo;
        $this->levelRepo = $levelRepo;
        $this->pickupRepo = $pickupRepo;
        
    }   

    public function getTransientRecords($argObj, $remainder) {

        $model = $argObj['model'];

        $timestamp = $this->pickupRepo->getLastInsertedForName($this->name);
        Log::info('Attribution beginning from timestamp: ' . $timestamp);

        $carbonDate = Carbon::createFromTimestamp($timestamp);

        // Checking whether attribution levels have changed since the last run
        $lastAttrLevelChange = Carbon::parse($this->levelRepo->getLastUpdate());

        if ('feedInvalidation' === $argObj['type']) {
            /*
                We need to get (EITHER) feed transients or all feed instances
                (or maybe something else)
            */

            $feedId = $argObj['feedId'];

            return $this->truthRepo->getFeedAttributions($feedId, $remainder);
        }
        elseif ('none' !== $model || $lastAttrLevelChange->gte($carbonDate)) {
            /* 
                If a model is specified, or if attribution has changed recently,
                we need to pick up all available transients. This is distinct from
                rerunning *all* records because it does not need to trawl the entire database -
                only two cases in the attribution flow chart.
                There *are* some cases that this will miss in the case of level change:
                something *would have* changed had the levels been different at some point in the past.
                However, this omission is deliberate - attribution only moves forward.
            */ 
            return $this->truthRepo->getFullTransients($remainder);
        }
        else {
            // Otherwise, run the optimized subset
            $datetime = $carbonDate->toDateTimeString();
            return $this->truthRepo->getOptimizedTransients($datetime, $remainder);
        }
        
    }

    public function run( $records , $modelId = 'none' , $userEmail = 'none' ) {

        $currentTimestamp = Carbon::now()->timestamp;

        $records->chunk(65000, function ($results) use ($modelId, $userEmail) {
            Artisan::call('attribution:processBatch', [
                'data' => $results, 
                'modelId' => $modelId ,
                'userEmail' => $userEmail
            ]);

            // This depends on the query completing faster than the processing job
            // This is currently a good assumption, but is not necessarily true
            Cache::increment($this->name);
        });

        // setting this to the start of the run prevents any gaps
        $this->pickupRepo->updatePosition($this->name, $currentTimestamp);
        
    }

}
