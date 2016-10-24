<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use App\Http\Requests;
use AdrianMejias\States\States;

use App\Services\ClientService;
#use App\Services\FeedService;

class ClientController extends Controller
{
    protected $states;
    protected $clientService;
    #protected $feedService;

    public function __construct ( States $states , ClientService $clientService /* , FeedService $feedService*/) {
        $this->states = $states;
        $this->clientService = $clientService;
        #$this->feedService = $feedService;
    }

    public function listAll () {
        return response()->view( 'bootstrap.pages.client.client-index' );
    }

    public function create () {
        $states = States::all();

        return response()->view( 'bootstrap.pages.client.client-add' , [ "states" => $states ] );
    }

    public function show ( $clientId ) {
        return response()->json( $this->clientService->getAccount( $clientId ) );
    }

    public function edit ( $clientId ) {
        $states = States::all();

        return response()->view( 'bootstrap.pages.client.client-update' , [
            "states" => $states,
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
