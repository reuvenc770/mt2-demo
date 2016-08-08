<?php

namespace App\Services;

use App\Repositories\CreativeClickthroughRateRepo;
use App\Repositories\FromOpenRateRepo;
use App\Repositories\SubjectOpenRateRepo;

class CfsStatsService {
    
    private $creativeRepo;
    private $fromRepo;
    private $subjectRepo;

    public function __construct(CreativeClickthroughRateRepo $creativeRepo, FromOpenRateRepo $fromRepo, SubjectOpenRateRepo $subjectRepo) {
        $this->creativeRepo = $creativeRepo;
        $this->fromRepo = $fromRepo;
        $this->subjectRepo = $subjectRepo;
    }

    // first - cfs for this offer - how has it performed for this offer?
    public function getCreativeOfferClickRate($offerId) {
        return $this->creativeRepo->getCreativeOfferClickRate($offerId);
    }

    public function getFromOfferOpenRate($offerId) {
        return $this->fromRepo->getFromOfferOpenRate($offerId);
    }

    public function getSubjectOfferOpenRate($offerId) {
        return $this->subjectRepo->getSubjectOfferOpenRate($offerId);
    }


    // second - cfses for the same offer, but how they've performed across all offers
    public function getGeneralCreativeClickRateUsingOffer($offerId) {
        return $this->creativeRepo->getGeneralCreativeClickRateUsingOffer($offerId);
    }

    public function getGeneralFromOpenRateUsingOffer($offerId) {
        return $this->fromRepo->getGeneralFromOpenRateUsingOffer($offerId);
    }

    public function getGeneralSubjectOpenRateUsingOffer($offerId) {
        return $this->subjectRepo->getGeneralSubjectOpenRateUsingOffer($offerId);
    }
}