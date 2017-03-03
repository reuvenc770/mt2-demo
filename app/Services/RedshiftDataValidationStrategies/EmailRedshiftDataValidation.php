<?php

namespace App\Services\RedshiftDataValidationStrategies;

use Log;
use App\Repositories\EmailRepo as CmpRepo;
use App\Repositories\RedshiftRepositories\EmailRepo as RedshiftRepo;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\S3RedshiftExportJob;

class EmailRedshiftDataValidation extends AbstractLargeRedshiftDataValidation {
    use DispatchesJobs;
    
    private $cmpRepo;
    private $redshiftRepo;
    private $badEmailSegments = [];
    const EMAIL_ACCEPTABLE_DIFF_RATE = 0.00001

    public function __construct(CmpRepo $cmpRepo, RedshiftRepo $redshiftRepo) {
        parent::__construct($cmpRepo, $redshiftRepo);
    }

    public function test($lookback) {
        if (!$this->statisticalTest()) {
            $this->testEmailDistribution();
            return false;
        }
        else {
            return true;
        }
    }

    public function fix() {
        if (count($this->badEmailSegments) > 0) {
            Log::critical("Discrepancies for email segments:" . implode(',', array_keys($this->badEmailSegments)));

            $lowest = ((int)min($this->badEmailSegments)) * 1000000; // return that million
            $version = 2; // Special job
            $job = new S3RedshiftExportJob('Email', $version, str_random(16), $lowest);
        }
    }

    protected function testEmailDistribution() {
        // These are assocs of the form [segment => count]
        $cmpAttrDist = $this->cmpRepo->getDistribution();
        $rsAttrDist = $this->redshiftRepo->getDistribution();

        // format: [segment => [cmpCount => #, rsCount => #]]
        $this->badEmailSegments = [];

        foreach($cmpAttrDist as $segment => $count) {
            if (isset($rsAttrDist[$segment])) {
                $rsCount = $rsAttrDist[$segment];

                if (!$this->isEqual($count, $rsCount)) {
                    $this->badEmailSegments[$segment] = ['cmpCount' => $count, 'rsCount' => $rsCount];
                }
            }
            elseif (!$this->equal($count, 0)) {
                // bad news, or just a new feed
                $this->badEmailSegments[$segment] = ['cmpCount' => $count, 'rsCount' => 0];
            }
        }

        // What about extras in rs?
    }

    protected function isEqual($cmpCount, $redshiftCount) {
        // Here we have to deal with the shifting email ids

        if ($cmpCount === $redshiftCount) {
            return true;
        }
        elseif (0 === $cmpCount) {
            return $redshiftCount < 10; // arbitrary, might be a better way to to this
        }
        else {
            $pctDiff = round(($redshiftCount - $cmpCount) / $cmpCount, 2);
            return abs($pctDiff) < self::EMAIL_ACCEPTABLE_DIFF_RATE; // allowed diff of <10 per million
        }
    }

}