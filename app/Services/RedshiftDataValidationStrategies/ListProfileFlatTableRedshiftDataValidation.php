<?php

namespace App\Services\RedshiftDataValidationStrategies;

use Log;
use Cache;
use Carbon\Carbon;
use App\Repositories\ListProfileFlatTableRepo as CmpRepo;
use App\Repositories\RedshiftRepositories\ListProfileFlatTableRepo as RedshiftRepo;
use App\Jobs\S3RedshiftExportJob;
use Illuminate\Foundation\Bus\DispatchesJobs;


class ListProfileFlatTableRedshiftDataValidation extends AbstractLargeRedshiftDataValidation {

    use DispatchesJobs;
    
    use DispatchesJobs;

    private $badEmailSegments = [];
    const SAMPLE_SIZE = 5000;
    private $lookback;

    private $cacheStorageTime = 120; // Redis key storage in minutes

    public function __construct(CmpRepo $cmpRepo, RedshiftRepo $redshiftRepo) {
        parent::__construct($cmpRepo, $redshiftRepo);
    }

    public function test($lookback) {
        $this->lookback = $lookback;

        if (!$this->statisticalTest()) {
            // Nothing else is remotely quick
            return false;
        }
        else {
            return true;
        }
    }

    public function fix() {
        $entity = 'ListProfileFlatTable';
        $version = 1; // repull last 10 days

        $job =  new S3RedshiftExportJob($entity, $version, str_random(16));
        $this->dispatch($job);
    }

    protected function statisticalTest() {
        // This statistical test will be slightly different
        $testStart = microtime(true);
        $i = 0;
        $matches = 0;
        $dates = $this->createDateRange($this->lookback);

        // Due to current setup, checking all days is unfortunately out of the question.

        $cmpTime = 0;
        $redshiftTime = 0; 

        $data = $this->redshiftRepo->getRandomSample($this->lookback, self::SAMPLE_SIZE);

        while ($i < self::SAMPLE_SIZE) {
            // pick a random date
            $date = array_rand($dates);

            // pick a deploy that has actions on that date
            $cmpStart = microtime(true);
            $deploys = Cache::tags('list_profile_flat_check')->get($date);

            if (null === $deploys) {
                $deploys = $this->cmpRepo->getDeploysOnDate($date);
                Cache::tags('list_profile_flat_check')->put($date, $deploys, $this->cacheStorageTime);
            }
            
            $deployId = array_rand($deploys);

            $cmpObj = $this->cmpRepo->deployDateSyncCheck($deployId, $date);
            $cmpEnd = microtime(true);
            $cmpTime = $cmpTime + ($cmpEnd - $cmpStart);

            if (null !== $cmpObj->clicks) {
                $redshiftStart = microtime(true);
                $rsObj = $this->redshiftRepo->findAggregation($deployId, $date);
                $redshiftEnd = microtime(true);
                $redshiftTime = $redshiftTime + ($redshiftEnd - $redshiftStart);

                // equality found here
                if (null === $rsObject) {
                    continue;
                }
                elseif ($this->isEqual($cmpObj->opens, $rsObject->opens) && 
                            $this->isEqual($cmpObj->clicks, $rsObject->clicks) && 
                            $this->isEqual($cmpObj->conversions, $rsObject->conversions)) {
                     $matches++;
                }
            }

            $i++;
        }

        Cache::tags('list_profile_flat_check')->flush();

        // sn = sqrt(np(1-p)). se is sn / sqrt(sample_size).
        $sampleStdDev = sqrt((self::SAMPLE_SIZE * ($matches / self::SAMPLE_SIZE) * ((self::SAMPLE_SIZE - $matches) / self::SAMPLE_SIZE)) );
        
        $testEnd = microtime(true);
        $testTime = $testEnd - $testStart;
        Log::info("ListProfileFlatTable has $matches matches out of " . self::SAMPLE_SIZE . " with sample std dev $sampleStdDev.");
        Log::info("ListProfileFlatTable took $testTime seconds. $redshiftTime for redshift and $cmpTime for CMP db.");

        return ($matches / self::SAMPLE_SIZE) > (self::IDEAL_CORRECT_RATE - (1.65 * ($sampleStdDev / sqrt(self::SAMPLE_SIZE))));
    }  


    private function createDateRange($lookback) {
        // returns an array of date strings
        $dates = [];

        // Doing days up to but not including today
        while ($lookback > 0) {
            $dates[] = Carbon::today()->subDays($lookback)->toDateString();
            $lookback--;
        }

        return $dates;
    }

}
