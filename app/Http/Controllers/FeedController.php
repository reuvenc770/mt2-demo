<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\MT1ApiService;
use App\Http\Requests\FeedEditRequest;
use App\Services\MT1Services\ClientService;
use App\Services\MT1Services\CountryService;
use Cache;

class FeedController extends Controller
{
    const CLIENT_API_ENDPOINT = 'clients_list';
    const CLIENT_UPDATE_API_ENDPOINT = 'client_update';
    const GEN_LINKS_API_ENDPOINT = 'gen_tracking_link';

    protected $api;
    protected $clientApi;
    protected $countryApi;
    protected $payoutService;

    public function __construct ( MT1ApiService $api , ClientService $clientApi , CountryService $countryApi ) {
        $this->api = $api;
        $this->clientApi = $clientApi;
        $this->countryApi = $countryApi;
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

    /**
     *
     */
    public function listAll () {
        return response()->view( 'pages.feed.feed-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countryList = $this->countryApi->getAll();
        return response()->view( 'pages.feed.feed-add' , [
            'countries' => ( !is_null( $countryList ) ? $countryList : [] ),
        ] );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FeedEditRequest $request)
    {
        Cache::tags( [ $this->clientApi->getType() ] )->flush();

        Flash::success("Feed was Successfully Updated");

        $response = response( $this->api->postForm( self::CLIENT_UPDATE_API_ENDPOINT , $request->all() ) );

        // temporarily picking off fields to be saved
        $response = json_decode($response, true);
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response = $this->api->getJSON( self::CLIENT_API_ENDPOINT , [ 'clientId' => $id ]);

        // mixing in variables from MT2
        $response = json_decode($response, true);
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $countryList = $this->countryApi->getAll() ?: [];
        return response()->view( 'pages.feed.feed-edit' , [
            'countries' =>  $countryList
        ] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FeedEditRequest $request, $id)
    {
        Cache::tags( [ $this->clientApi->getType() ] )->flush();

        Flash::success("Feed was Successfully Updated");

        return response( $this->api->postForm( self::CLIENT_UPDATE_API_ENDPOINT , $request->all() ) );
    }

    public function generateLinks ( $id ) {
        return response( $this->api->getJSON( self::GEN_LINKS_API_ENDPOINT , array( 'cid' => $id ) ) );
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

    public function resetClientPassword($username) {
        $this->clientApi->resetPassword($username);
        return response()->json( [ 'status' => true ] );
    }
}
