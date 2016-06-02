<?php

namespace App\Http\Controllers;

use App\Facades\Suppression;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\MT1ApiService;

class ShowInfoController extends Controller
{
    const SHOW_INFO_ENDPOINT = 'show_info_2';
    const SUPPRESSION_ENDPPOINT = '';

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
        return view( 'pages.show-info' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response( 'Unauthorized' , 401 );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response( json_encode( [ 'status' => 1 , 'message' => 'Record Suppressed' ] ) );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        $type = 'email';
        if ( preg_match( "/\d{1,}/" , $id ) ) $type = 'eid';

        $apiResponse = $this->api->getJson(
            self::SHOW_INFO_ENDPOINT ,
            [ 'type' => $type , 'id' => $id ]
        );

        if ( $apiResponse === false ) $apiResponse = json_encode( [] );
        $apiResponse = Suppression::convertSuppressionReason(json_decode($apiResponse));

        return response( $apiResponse );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response( 'Unauthorized' , 401 );
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
        return response( 'Unauthorized' , 401 );
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
