<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\FeedApiRecordRequest;

use App\Services\FeedApiService;

class FeedApiController extends Controller
{
    protected $service;

    public function __construct ( FeedApiService $service ) {
        $this->service = $service;
    }

    public function ingest ( FeedApiRecordRequest $request ) {
        $this->service->setRequestInfo(
            $this->service->getFeedIdFromPassword( $request->input( 'pw' ) ) ,
            $request->fullUrl() ,
            $request->ip()
        );
            
        $ingestionResponse = $this->service->ingest( $request->all() );

        return response()->json( $ingestionResponse );
    }
}
