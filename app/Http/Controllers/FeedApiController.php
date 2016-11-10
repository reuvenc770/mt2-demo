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
        $ingestionResponse = $this->service->ingest( $request->all() );

        return response()->json( $ingestionResponse );
    }
}
