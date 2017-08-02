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

        while ($i < self::SAMPLE_SIZE) {
            $i++;

            // pick a random date
            $dateKey = array_rand($dates);
            $date = $dates[$dateKey];

            // pick a deploy that has actions on that date
            $cmpStart = microtime(true);
            $deploys = Cache::tags('list_profile_flat_check')->get($date);

            if (null === $deploys) {
                $deploys = $this->cmpRepo->getDeploysOnDate($date);
                Cache::tags('list_profile_flat_check')->put($date, $deploys, $this->cacheStorageTime);
            }
            
            $deployIndex = array_rand($deploys);
            $deployId = $deploys[$deployIndex];

            $cmpObj = $this->cmpRepo->deployDateSyncCheck($deployId, $date);
            $cmpEnd = microtime(true);
            $cmpTime = $cmpTime + ($cmpEnd - $cmpStart);

            if (null !== $cmpObj->clicks) {
                $redshiftStart = microtime(true);
                $rsObj = $this->redshiftRepo->findAggregation($deployId, $date);
                $redshiftEnd = microtime(true);
                $redshiftTime = $redshiftTime + ($redshiftEnd - $redshiftStart);

                // equality found here
                if (null === $rsObj) {
                    continue;
                }
                elseif ($this->isEqual($cmpObj->opens, $rsObj->opens) && 
                            $this->isEqual($cmpObj->clicks, $rsObj->clicks) && 
                            $this->isEqual($cmpObj->conversions, $rsObj->conversions)) {
                    $matches++;
                }
            }
        }

        Cache::tags('list_profile_flat_check')->flush();

        // se is sqrt(p(1-p) / sample_size).
        $stdErr = sqrt((($matches / self::SAMPLE_SIZE) * ((self::SAMPLE_SIZE - $matches) / self::SAMPLE_SIZE) ) / self::SAMPLE_SIZE);
        
        $testEnd = microtime(true);
        $testTime = $testEnd - $testStart;

        Log::info("ListProfileFlatTable has $matches matches out of " . self::SAMPLE_SIZE . " for a match rate of " . round($matches / self::SAMPLE_SIZE, 3) . " with a standard error of $stdErr.");
        Log::info("ListProfileFlatTable took $testTime seconds. $redshiftTime for redshift and $cmpTime for CMP db.");

        return ($matches / self::SAMPLE_SIZE) > (self::IDEAL_CORRECT_RATE - (1.65 * $stdErr));
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
