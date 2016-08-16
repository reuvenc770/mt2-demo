<?php

namespace App\Services;

use App\Repositories\CreativeClickthroughRateRepo;
use App\Repositories\CreativeRepo;
use App\Repositories\FromOpenRateRepo;
use App\Repositories\FromRepo;
use App\Repositories\SubjectOpenRateRepo;
use App\Repositories\SubjectRepo;

class CfsStatsService {
    
    private $creativeRepo;
    private $subjectRepo;
    private $fromLine;

    public function __construct(CreativeRepo $creativeRepo, SubjectRepo $subjectRepo, FromRepo $fromLineRepo) {
        $this->creativeRepo = $creativeRepo;
        $this->subjectRepo = $subjectRepo;
        $this->fromLine = $fromLineRepo;
    }

    // first - cfs for this offer - how has it performed for this offer?
    public function getCreativeOfferClickRate($offerId) {
        return $this->creativeRepo->getCreativeOfferClickRate($offerId);
    }

    public function getFromOfferOpenRate($offerId) {
        return $this->fromLine->getFromOfferOpenRate($offerId);
    }

    public function getSubjectOfferOpenRate($offerId) {
        return $this->subjectRepo->getSubjectOfferOpenRate($offerId);
    }



}