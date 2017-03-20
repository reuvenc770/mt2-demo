<?php

namespace App\Services\RedshiftDataValidationStrategies;

use Log;

abstract class AbstractLargeRedshiftDataValidation {

    protected $cmpRepo;
    protected $redshiftRepo;
    const IDEAL_CORRECT_RATE = 0.999;
    const TEST_COUNT = 10000;
    

    public function __construct($cmpRepo, $redshiftRepo) {
        $this->cmpRepo = $cmpRepo;
        $this->redshiftRepo = $redshiftRepo;
    }

    public abstract function test($lookback);

    public abstract function fix();

    protected function statisticalTest() {
        /*
            Step 1: Statistical comparison.
            We could compare the two databases row-by-row, but this is incredibly slow and would never catch up.
            We could run a small test of just the latest data, but this ignores possible changes elsewhere 
            (and ignores the fact that data might change without notification).

            Our first test is to run a set number (10k now) random spot checks on both tables.
            This number is both small enough to be finished relatively quickly and large enough to get good results
            We check if the difference rate is statistically likely to be within our threshold (99% confidence, one-sided)
            using Student's one sample T-test with one degree of freedom.
        */

        $i = 0;
        $matches = 0;
        $testStart = microtime(true);

        list($minEmailId, $maxEmailId) = $this->cmpRepo->getMinAndMaxIds();

        $cmpTime = 0;
        $redshiftTime = 0;

        while ($i < self::TEST_COUNT) {
            // 1. Get a random email id from the source
            $testEmailId = mt_rand($minEmailId, $maxEmailId);
            $cmpStart = microtime(true);
            $cmpObj = $this->cmpRepo->get($testEmailId);
            $cmpEnd = microtime(true);
            $cmpTime = $cmpTime + ($cmpEnd - $cmpStart);

            if ($cmpObj !== null) {
                $redshiftStart = microtime(true);
                if ($this->redshiftRepo->matches($cmpObj)) {
                    $matches++;
                }
                $redshiftEnd = microtime(true);
                $redshiftTime = $redshiftTime + ($redshiftEnd - $redshiftStart);
                $i++;
            }
        }

        $testEnd = microtime(true);
        $testTime = $testEnd - $testStart;
        $cmpClass = explode('\\', get_class($this->cmpRepo))[2];
        $entity = str_replace('Repo', '', $cmpClass);
        
        // sn = sqrt(np(1-p)). se is sn / sqrt(sample_size).
        $sampleStdDev = sqrt(self::TEST_COUNT * ($matches / self::TEST_COUNT) * ((self::TEST_COUNT - $matches) / self::TEST_COUNT) );
        
        Log::info("$entity has $matches matches out of " . self::TEST_COUNT . " with a standard deviation of $sampleStdDev.");
        Log::info("$entity took $testTime seconds. $redshiftTime for redshift and $cmpTime for CMP db.");

        return ($matches / self::TEST_COUNT) > (self::IDEAL_CORRECT_RATE - (1.65 * ($sampleStdDev / sqrt(self::TEST_COUNT))));
    }  

    protected function isEqual($cmpCount, $redshiftCount) {
        if ($cmpCount === $redshiftCount) {
            return true;
        }
        elseif (0 === $cmpCount) {
            return $redshiftCount < 10; // arbitrary, might be a better way to to this
        }
        else {
            $pctDiff = round(($redshiftCount - $cmpCount) / $cmpCount, 2);
            return abs($pctDiff) < self::ACCEPTABLE_DIFF_RATE;
        }
    }
}