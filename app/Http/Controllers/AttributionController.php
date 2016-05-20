<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\MT1ApiService;
use App\Http\Requests\AttributionPostRequest;
use App\Services\MT1Services\ClientAttributionService;
use Laracasts\Flash\Flash;

class AttributionController extends Controller
{
    const ATTRIBUTION_UPLOAD_ENDPOINT ="attribution_update";

    protected $api;
    protected $attributionApi;

    public function __construct ( MT1ApiService $api, ClientAttributionService $attrService  ) {
        $this->api = $api;
        $this->attributionApi = $attrService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $clients = $this->attributionApi->getClientList( $request->input( 'page' ) , $request->input( 'count' ) );

        return response( $clients );
    }

    public function listAll () {
        return response()->view( 'pages.client_attribution' );
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
    public function store( AttributionPostRequest $request )
    {
        $response = [ 'status' => false ];

        $postResponse =  $this->api->postForm( self::ATTRIBUTION_UPLOAD_ENDPOINT , $request->all() );

        $responseArr = json_decode( $postResponse , true );

        if ( array_key_exists( 'status' , $responseArr )  )  {
            $response[ 'status' ] = true;
            $this->attributionApi->flushCache();
        }

        return response()->json( $response );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response( 'Unauthorized' , 401 );
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
