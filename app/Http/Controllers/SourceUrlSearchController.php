<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\ClientService;
use App\Services\FeedService;

class SourceUrlSearchController extends Controller
{
    protected $feedService;
    protected $ClientService;

    public function __construct ( FeedService $feedService , ClientService $clientService) {
        $this->feedService = $feedService;
        $this->clientService = $clientService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return response()->view( "pages.source-url-search" , [
            'feedVerticals' => $this->feedService->getVerticals()->toJson(),
            'clients' => $this->clientService->get()->toJson()
        ] );
    }
}
