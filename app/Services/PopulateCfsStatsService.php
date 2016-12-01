<?php

namespace App\Services;

use App\Repositories\DeployRepo;
use App\Repositories\StandardApiReportRepo;
use App\Repositories\CreativeClickthroughRateRepo;
use App\Repositories\FromOpenRateRepo;
use App\Repositories\SubjectOpenRateRepo;
use Carbon\Carbon;

class PopulateCfsStatsService
{

    private $deployRepo;
    private $subjRepo;
    private $fromRepo;
    private $creativeRepo;
    private $reportRepo;
    private $mt1DeployData;

    public function __construct(DeployRepo $deployRepo,
                                StandardApiReportRepo $reportRepo,
                                CreativeClickthroughRateRepo $creativeRepo,
                                FromOpenRateRepo $fromRepo,
                                SubjectOpenRateRepo $subjRepo) {

        $this->deployRepo = $deployRepo;
        $this->subjRepo = $subjRepo;
        $this->fromRepo = $fromRepo;
        $this->creativeRepo = $creativeRepo;
        $this->reportRepo = $reportRepo;
    }

    public function extract($lookback) {
        $date = Carbon::today()->subDays($lookback)->format('Y-m-d');
        $this->mt1DeployData = $this->deployRepo->getUpdatedFrom($date);
    }

    public function load() {
        foreach ($this->mt1DeployData as $deploy) {
            $deployId = $deploy->id;
            $creativeId = $deploy->creative_id;
            $subjectId = $deploy->subject_id;
            $fromId = $deploy->from_id;
            $listProfileCombineId = $deploy->list_profile_combine_id;
            $stats = $this->reportRepo->getStatsForDeploy($deployId);
            if ($stats) {
                $delivers = (int)$stats->delivers;
                $opens = (int)$stats->opens;
                $clicks = (int)$stats->clicks;

                $this->creativeRepo->saveStats($creativeId, $listProfileCombineId, $deployId, $delivers, $opens, $clicks);
                $this->subjRepo->saveStats($subjectId, $listProfileCombineId, $deployId, $delivers, $opens);
                $this->fromRepo->saveStats($fromId, $listProfileCombineId, $deployId, $delivers, $opens);
            }
            
        }

    }
}