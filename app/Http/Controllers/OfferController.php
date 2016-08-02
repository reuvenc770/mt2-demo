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
        $term = $request->input("searchTerm");
        $offers = $this->offerService->autoCompleteSearch($term);
        return response()->json($offers);
    }
}
