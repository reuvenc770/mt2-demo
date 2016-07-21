<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\MT1ApiService;
use App\Http\Requests\ClientEditRequest;
use App\Services\MT1Services\ClientService;
use App\Services\MT1Services\CountryService;
use App\Services\ClientPayoutService;
use Cache;

class ClientController extends Controller
{
    const CLIENT_API_ENDPOINT = 'clients_list';
    const CLIENT_UPDATE_API_ENDPOINT = 'client_update';
    const GEN_LINKS_API_ENDPOINT = 'gen_tracking_link';

    protected $api;
    protected $clientApi;
    protected $countryApi;
    protected $payoutService;

    public function __construct ( MT1ApiService $api , ClientService $clientApi , CountryService $countryApi, ClientPayoutService $payoutService ) {
        $this->api = $api;
        $this->clientApi = $clientApi;
        $this->countryApi = $countryApi;
        $this->payoutService = $payoutService;
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
        return response()->view( 'pages.client.client-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countryList = $this->countryApi->getAll();
        $payoutTypes = $this->payoutService->getTypes() ?: [];
        return response()->view( 'pages.client.client-add' , [
            'countries' => ( !is_null( $countryList ) ? $countryList : [] ),
            'payoutTypes' => $payoutTypes
        ] );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientEditRequest $request)
    {
        Cache::tags( [ $this->clientApi->getType() ] )->flush();

        Flash::success("Client was Successfully Updated");

        $response = response( $this->api->postForm( self::CLIENT_UPDATE_API_ENDPOINT , $request->all() ) );
        
        // temporarily picking off fields to be saved
        $response = json_decode($response, true);
        $clientId = $response['client_id'];
        $payoutType = $request->input('payout_type');
        $payoutAmount = $request->input('payout_amount');
        $this->payoutService->setPayout($clientId, $payoutType, $payoutAmount);

        return $response;
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
        $payout = $this->payoutService->getPayout($id)->toArray();
        $response[0]['payout_type'] = $payout['client_payout_type_id'];
        $response[0]['payout_amount'] = $payout['amount'];

        return json_encode($response);
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
        $payoutTypes = $this->payoutService->getTypes()->toArray() ?: '';
        return response()->view( 'pages.client.client-edit' , [ 
            'countries' =>  $countryList, 
            'payoutTypes' => $payoutTypes
        ] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ClientEditRequest $request, $id)
    {
        Cache::tags( [ $this->clientApi->getType() ] )->flush();

        Flash::success("Client was Successfully Updated");

        // temporarily picking off fields to be saved
        $clientId = $request->input('client_id');
        $payoutType = $request->input('payout_type');
        $payoutAmount = $request->input('payout_amount');

        $this->payoutService->setPayout($clientId, $payoutType, $payoutAmount);

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
