<?php

namespace App\Services\RedshiftDataValidationStrategies;

use Log;
use App\Repositories\EmailAttributableFeedLatestDataRepo as CmpRepo;
use App\Repositories\RedshiftRepositories\RecordDataRepo as RedshiftRepo;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\S3RedshiftExportJob;

class RecordDataRedshiftDataValidation extends AbstractLargeRedshiftDataValidation {
    use DispatchesJobs;

    private $badDays = [];

    public function __construct(CmpRepo $cmpRepo, RedshiftRepo $redshiftRepo) {
        parent::__construct($cmpRepo, $redshiftRepo);
    }


    public function test($lookback) {
        if (!$this->statisticalTest()) {
            $this->testActionDateDistribution();
            return false;
        }
        else {
            return true;
        }
    }
    

    public function fix() {
        if (count($this->badDays) > 0) {
            $minDate = min(array_keys($this->badDays));

            Log::critical("Large discrepancies for record data beginning on $minDate.");
            Log::critical($this->badDays);

            $version = 2; // Special job
            $job = new S3RedshiftExportJob('RecordData', $version, str_random(16), $minDate);
        }
    }

    private function testActionDateDistribution() {

        // These are assocs of the form [day => count of actions]
        $cmpAttrDist = $this->cmpRepo->getActionDateDistribution();
        $rsAttrDist = $this->redshiftRepo->getActionDateDistribution();

        // format: [day => [cmpCount => #, rsCount => #]]
        $this->badDays = [];

        foreach($cmpAttrDist as $day => $count) {
            if (isset($rsAttrDist[$day])) {
                $rsCount = $rsAttrDist[$day];

                if (!$this->isEqual($count, $rsCount)) {
                    $this->badDays[$day] = ['cmpCount' => $count, 'rsCount' => $rsCount];
                }
            }
            elseif (!$this->equal($count, 0)) {
                // bad news, or just a new feed
                $this->badDays[$day] = ['cmpCount' => $count, 'rsCount' => 0];
            }
        }
    }
    
}