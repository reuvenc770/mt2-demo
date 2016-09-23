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

    public function getTransientRecords($remainder, $model) {

        $timestamp = $this->pickupRepo->getLastInsertedForName($this->name);
        Log::info('Attribution beginning from timestamp: ' . $timestamp);

        $carbonDate = Carbon::createFromTimestamp($timestamp);

        // Checking whether attribution levels have changed since the last run
        $lastAttrLevelChange = Carbon::parse($this->levelRepo->getLastUpdate());

        if ('none' !== $model || $lastAttrLevelChange->gte($carbonDate)) {
            // If a model is specified, or if attribution has changed recently,
            // execute the full run
            return $this->truthRepo->getFullTransients($remainder);
        }
        else {
            // Otherwise, run the optimized subset
            $datetime = $carbonDate->toDateTimeString();
            return $this->truthRepo->getOptimizedTransients($datetime, $remainder);
        }
        
    }

    public function run( $records , $modelId = 'none' ) {

        $currentTimestamp = Carbon::now()->timestamp;
        Cache::forever($this->name, 0);

        $records->chunk(65000, function ($results) use ($modelId) {
            Artisan::call('attribution:processBatch', [
                'data' => $results, 
                'modelId' => $modelId
            ]);

            Cache::increment($this->name);
        });

        // setting this to the start of the run prevents any gaps
        $this->pickupRepo->updatePosition($this->name, $currentTimestamp);
        
    }

}
