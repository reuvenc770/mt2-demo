<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\MT1ApiService;

class ClientController extends Controller
{
    const CLIENT_API_ENDPOINT = 'clients_list';
    const CLIENT_UPDATE_API_ENDPOINT = 'client_update';

    protected $api;

    public function __construct ( MT1ApiService $api ) {
        $this->api = $api;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response( $this->api->getJSON( self::CLIENT_API_ENDPOINT ) );
    }

    public function pager ( Request $request ) {
        if ( $request->input( 'disablecache' ) == 1 ) {
            return $this->index();
        } else {
            return response( $this->api->getPaginatedJson( self::CLIENT_API_ENDPOINT , $request->input( 'page' ) , $request->input( 'count' )) );
        }
    }

    /**
     *
     */
    public function listAll () {
        return response()->view( 'pages.client.client-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view( 'pages.client.client-add' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response( $this->api->postForm( self::CLIENT_UPDATE_API_ENDPOINT , $request->all() ) );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response( $this->api->getJSON( self::CLIENT_API_ENDPOINT , [ 'clientId' => $id ] ) ); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->view( 'pages.client.client-edit' );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return response( $this->api->postForm( self::CLIENT_UPDATE_API_ENDPOINT , $request->all() ) );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response( 'Unauthorized' , 401 );
    }
}
