<?php

namespace App\Http\Controllers;

use App\Services\CfsStatsService;
use Illuminate\Http\Request;

use App\Http\Requests;

class CreativeFromSubjectController extends Controller
{
   protected $cfsService;

    public function __construct(CfsStatsService $service )
    {
        $this->cfsService  = $service;
    }


    public function getCreatives($offerId){

        return $this->cfsService->getCreativeOfferClickRate($offerId);
    }

    public function getFroms($offerId){

        return $this->cfsService->getFromOfferOpenRate($offerId);
    }

    public function getSubjects($offerId){

        return $this->cfsService->getSubjectOfferOpenRate($offerId);
    }


    public function previewCreative(Request $request ,$offerId){
        $creatives  = $this->cfsService->getCreativeByOfferId($offerId);

         return response()
            ->view( 'bootstrap.pages.cfs.creative-preview', ["creatives" => $creatives] );
    }
}

