<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 8/1/16
 * Time: 5:25 PM
 */

namespace App\Services;


use App\Repositories\OfferRepo;

class OfferService
{
    protected $offerRepo;

    public function __construct(OfferRepo $offerRepo)
    {
        $this->offerRepo = $offerRepo;
    }

    public function all () {
        return $this->offerRepo->all();
    }

    public function autoCompleteCpmSearch ( $term ) {
        return $this->offerRepo->fuzzyCpmSearchBack( $term );
    } 

    public function autoCompleteSearch($day, $term){
        return $this->offerRepo->fuzzySearchBack($day, $term);
    }
    public function searchByDay($day){
        return $this->offerRepo->searchByDay($day);
    }

}
