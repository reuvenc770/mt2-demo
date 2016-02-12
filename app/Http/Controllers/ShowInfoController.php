<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\MT1ApiService;

class ShowInfoController extends Controller
{
    const SHOW_INFO_ENDPOINT = '/newcgi-bin/show_info_2.cgi';
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $apiResponse = $this->api->getJson(
            self::SHOW_INFO_ENDPOINT ,
            [ 'type' => $request->input( 'type' ) , 'id' => $request->input( 'id' ) ]
        );

        $responseCode = ( $apiResponse === false ? 500 : 200 );

        if ( $responseCode === 500 ) $apiResponse = json_encode( [] );

        return response( $apiResponse , $responseCode );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
