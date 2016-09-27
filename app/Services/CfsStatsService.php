<?php

namespace App\Services;

use App\Models\Creative;
use App\Repositories\CreativeClickthroughRateRepo;
use App\Repositories\CreativeRepo;
use App\Repositories\FromOpenRateRepo;
use App\Repositories\FromRepo;
use App\Repositories\SubjectOpenRateRepo;
use App\Repositories\SubjectRepo;
//TODO: more Generic name, more specific repository?  or more another service same repo for crud actions on CFS
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
        return $this->displaySort($this->creativeRepo->getCreativeOfferClickRate($offerId));
    }

    public function getFromOfferOpenRate($offerId) {
        return $this->displaySort($this->fromLine->getFromOfferOpenRate($offerId));
    }

    public function getSubjectOfferOpenRate($offerId) {
        return $this->displaySort($this->subjectRepo->getSubjectOfferOpenRate($offerId));
    }

    /**
     *  Display sort:
     *  make sure that sends from yesterday are at the very bottom of the list
     */

    private function displaySort($data) {        
        $nonYesterday = [];
        $yesterday = [];

        foreach($data as $row) {
            if (1 >= (int)$row['days_ago']) {
                $yesterday[] = $row;
            }
            else {
                $nonYesterday[] = $row;
            }
        }

        return array_merge($nonYesterday, $yesterday);
    }


    /**
     * Crud stuff
     */


    public function getCreativeByOfferId($offerId){
       $creatives = $this->creativeRepo->getCreativesByOffer($offerId);

        foreach($creatives as $creative){
            $creative->creative_html = $this->parseReplaceHtml($creative->creative_html);
        }
        return $creatives;
    }


    private function parseReplaceHtml($html){
       $html = preg_replace("/{{IMG_DOMAIN}}/", Creative::DEFAULT_DOMAIN, $html);
        return $html;
    }



}