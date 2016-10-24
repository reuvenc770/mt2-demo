<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use App\Http\Requests;

use App\Services\ClientService;
#use App\Services\FeedService;

class ClientController extends Controller
{
    protected $clientService;
    #protected $feedService;

    public function __construct ( ClientService $clientService /* , FeedService $feedService*/) {
        $this->clientService = $clientService;
        #$this->feedService = $feedService;
    }

    public function listAll () {
        $this->response()->view( 'bootstrap.pages.client.client-index' );
    }

    public function create () {
        $this->response()->view( 'bootstrap.pages.client.client-add' );
    }

    public function edit ( $clientId ) {
        $this->response()->view( 'bootstrap.pages.client.client-update' , [
            'clientId' => $clientId ,
            'feeds' => [] #Need to inject feeds, its in another branch melissa is working on.
        ] );
    }

    public function store ( ClientStoreRequest $request ) {
        Flash::success( 'Client was successfully created.' );

        return response()->json( [ 'message' => 'dummy response' ] );
    }

    public function update ( ClientUpdateRequest $request ) {
        Flash::success( 'Client was successfully updated.' );

        return response()->json( [ 'message' => 'dummy response' ] );
    }

    public function destroy ( $id ) {
        Flash::success( 'Client was successfully deleted.' );

        return response()->json( [ 'message' => 'dummy response' ] );
    }
}
