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


    public function typeAheadSearch(Request $request){
        $day = $request->input("day");
        $term = $request->input("searchTerm");
        if(isset($term)) {
            $offers = $this->offerService->autoCompleteSearch($day, $term);
        } else {
            $offers = $this->offerService->searchByDay($day);
        }
        return response()->json($offers);
    }
}
