<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Services\AttributionModelService;
use App\Http\Requests\AttributionModelRequest;

use Log;

class AttributionModelController extends Controller
{
    protected $service;

    public function __construct ( AttributionModelService $service ) {
        $this->service = $service; 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function listAll () {
        return response()->view( 'pages.attribution.attribution-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view( 'pages.attribution.attribution-add' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributionModelRequest $request)
    {
        return response()->json( [ $this->service->create( $request->input( 'name' ) , $request->input( 'levels' ) ) ] );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json( $this->service->get( $id ) );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->view( 'pages.attribution.attribution-edit' );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributionModelRequest $request, $id)
    {
        return response()->json( [
            'status' => $this->service->updateModel( $id , $request->input( 'name' ) , $request->input( 'levels' ) )
        ] );
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

    public function levels ( $modelId ) {
        return response()->json( $this->service->levels( $modelId ) );
    }
    
    public function copyLevels ( Request $request ) {
        return response()->json( [ "status" => 
            $this->service->copyLevels(
                $request->input( 'currentModelId' ) ,
                $request->input( 'templateModelId' )
            )
        ] );
    }

    public function getModelClients ( $modelId ) {
        Log::info( 'getModelClients' );
        return response()->json( $this->service->getModelClients( $modelId ) );
    }
}
