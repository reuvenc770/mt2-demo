<?php

namespace App\Http\Controllers;

use App\Services\OfferService;
use Illuminate\Http\Request;

use App\Http\Requests;

class OfferController extends Controller
{
    protected $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }


    public function typeAheadDaySearch(Request $request){
        $day = $request->input("day");
        $term = $request->input("searchTerm");
        $cpmSearch = $request->input( 'cpm' );

        if(isset($term)) {
            $offers = $this->offerService->autoCompleteSearch($term, $day);
        } else {
            $offers = $this->offerService->searchByDay($day);
        }
        return response()->json($offers);
    }

    public function typeAheadSearch(Request $request){
        $term = $request->input("searchTerm");

        if(isset($term)) {
            $offers = $this->offerService->autoCompleteGeneralSearch($term);
        }

        return response()->json($offers);
    }
}
