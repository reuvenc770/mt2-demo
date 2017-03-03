<?php

namespace App\Services\RedshiftDataValidationStrategies;

use Log;
use App\Repositories\EmailFeedAssignmentRepo as CmpRepo;
use App\Repositories\RedshiftRepositories\EmailFeedAssignmentRepo as RedshiftRepo;
use App\Jobs\S3RedshiftExportJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class EmailFeedAssignmentRedshiftDataValidation extends AbstractLargeRedshiftDataValidation {
    use DispatchesJobs;

    private $cmpRepo;
    private $redshiftRepo;
    private $badFeeds = [];

    public function __construct(CmpRepo $cmpRepo, RedshiftRepo $redshiftRepo) {
        $this->cmpRepo = $cmpRepo;
        $this->redshiftRepo = $redshiftRepo;
    }

    public function test($lookback) {
        if (!$this->statisticalTest()) {
            $this->testAttributionDistribution();
            return false;
        }
        else {
            return true;
        }
    }

    public function fix() {
        if (count($this->badFeeds) > 0) {
            Log::critical("Serious attribution discrepancies discovered for feeds:" . implode(',', array_keys($this->badFeeds)));
            Log::critical($this->badFeeds);

            // Reload all of these feeds
            $version = 2; // Special job
            $job = new S3RedshiftExportJob('Email', $version, str_random(16), array_keys($this->badFeeds));
        }
    }

    private function testAttributionDistribution() {
        // These are assocs of the form [feed id => count]
        $cmpAttrDist = $this->cmpRepo->getAttributionDist();
        $rsAttrDist = $this->redshiftRepo->getAttributionDist();

        // format: [feedId => [cmpCount => #, rsCount => #]]
        $this->badFeeds = [];

        foreach($cmpAttrDist as $feedId => $count) {
            if (isset($rsAttrDist[$feedId])) {
                $rsCount = $rsAttrDist[$feedId];

                if (!$this->isEqual($count, $rsCount)) {
                    $this->badFeeds[$feedId] = ['cmpCount' => $count, 'rsCount' => $rsCount];
                }
            }
            elseif (!$this->equal($count, 0)) {
                // bad news, or just a new feed
                $this->badFeeds[$feedId] = ['cmpCount' => $count, 'rsCount' => 0];
            }
        }
    }
    
}