<?php

namespace App\Services;

use App\Repositories\MT1Repositories\EspAdvertiserJoinRepo;
use App\Repositories\StandardApiReportRepo;
use App\Repositories\CreativeClickthroughRateRepo;
use App\Repositories\FromOpenRateRepo;
use App\Repositories\SubjectOpenRateRepo;
use Carbon\Carbon;

class PopulateCfsStatsService
{

    private $mt1Repo;
    private $subjRepo;
    private $fromRepo;
    private $creativeRepo;
    private $reportRepo;
    private $mt1DeployData;

    public function __construct(EspAdvertiserJoinRepo $mt1Repo,
                                StandardApiReportRepo $reportRepo,
                                CreativeClickthroughRateRepo $creativeRepo,
                                FromOpenRateRepo $fromRepo,
                                SubjectOpenRateRepo $subjRepo) {

        $this->mt1Repo = $mt1Repo;
        $this->subjRepo = $subjRepo;
        $this->fromRepo = $fromRepo;
        $this->creativeRepo = $creativeRepo;
        $this->reportRepo = $reportRepo;
    }

    public function extract($lookback) {
        $date = Carbon::today()->subDays($lookback)->format('Y-m-d');
        $this->mt1DeployData = $this->mt1Repo->getUpdatedFrom($date);
    }

    public function load() {
        foreach ($this->mt1DeployData as $deploy) {
            $deployId = $deploy->deploy_id;
            $creativeId = $deploy->creative_id;
            $subjectId = $deploy->subject_id;
            $fromId = $deploy->from_id;

            $stats = $this->reportRepo->getStatsForDeploy($deployId);
            if ($stats) {
                $delivers = (int)$stats->delivers;
                $opens = (int)$stats->opens;
                $clicks = (int)$stats->clicks;

                // Currently list profile id is 0 because MT1 does not save that information
                $listProfileId = 0;
                $this->creativeRepo->saveStats($creativeId, $listProfileId, $deployId, $opens, $clicks);
                $this->subjRepo->saveStats($subjectId, $listProfileId, $deployId, $delivers, $opens);
                $this->fromRepo->saveStats($fromId, $listProfileId, $deployId, $delivers, $opens);
            }
            
        }

    }
}