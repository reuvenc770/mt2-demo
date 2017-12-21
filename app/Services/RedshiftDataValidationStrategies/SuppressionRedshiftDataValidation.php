<?php

namespace App\Services\RedshiftDataValidationStrategies;

use DB;
use PDO;
use Log;
use App\Jobs\S3RedshiftExportJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class SuppressionRedshiftDataValidation {
    use DispatchesJobs;
    
    private $cmpRepo;
    private $redshiftRepo;
    private $lookback;

    public function __construct($cmpRepo, $redshiftRepo) {
        $this->cmpRepo = $cmpRepo;
        $this->redshiftRepo = $redshiftRepo;
    }

    public function test($lookback) {
        $this->lookback = $lookback;
        // These will check _before_ a certain date.
        // Today's data will likely be out of sync
        $cmpCount = $this->cmpRepo->getCount($lookback);
        $rsCount = $this->redshiftRepo->getCount($lookback);
        Log::info("Global suppression count: CMP: $cmpCount. Redshift: $rsCount");

        return $cmpCount === $rsCount;
    }

    public function fix() { 
        $version = 1; // extended
        $job = new S3RedshiftExportJob('SuppressionGlobalOrange', $version, str_random(16), null, '1h');
        $this->dispatch($job);
    }
}