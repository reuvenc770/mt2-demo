<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use App\Http\Requests;
use AdrianMejias\States\States;
use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;

use App\Services\ClientService;

class ClientController extends Controller
{
    protected $states;
    protected $clientService;

    public function __construct ( States $states , ClientService $clientService ) {
        $this->states = $states;
        $this->clientService = $clientService;
    }

    public function listAll () {
        return response()->view( 'pages.client.client-index' );
    }

    public function create () {
        $states = States::all();

        return response()->view( 'pages.client.client-add' , [ "states" => $states ] );
    }

    public function show ( $clientId ) {
        return response()->json( $this->clientService->getAccount( $clientId ) );
    }

    public function edit ( $clientId ) {
        $states = States::all();

        return response()->view( 'pages.client.client-update' , [
            "clientData" => $this->clientService->getAccount( $clientId )->toJSON() ,
            "states" => $states,
            'clientId' => $clientId ,
            'feeds' => $this->clientService->getFeeds( $clientId )
        ] );
    }

    public function store ( ClientStoreRequest $request ) {
        Flash::success( 'Client was successfully created.' );

        $this->clientService->updateOrCreate( $request->all() );

        return response()->json( [ 'status' => true ] );
    }

    public function update ( ClientUpdateRequest $request , $id ) {
        Flash::success( 'Client was successfully updated.' );

        $this->clientService->updateOrCreate( $request->all() );

        return response()->json( [ 'status' => true ] );
    }

    public function destroy ( $id ) {
        Flash::success( 'Client was successfully deleted.' );

        return response()->json( [ 'message' => 'dummy response' ] );
    }
}
