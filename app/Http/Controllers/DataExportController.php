<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MT1ApiService;
use App\Http\Requests;

class DataExportController extends Controller
{
    protected $api;
    const DATA_EXPORT_API_ENDPOINT = 'dataexports';
    
    public function __construct (MT1ApiService $api) {
       $this->api = $api;
    }

    public function status(Request $request) {
        return response($this->api->getJSON(self::DATA_EXPORT_API_ENDPOINT, $request->all()));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        return response( $this->api->getJSON( self::DATA_EXPORT_API_ENDPOINT , $request->all()) );
    }

    public function listActive() {
        return response()->view('pages.dataexport.dataexport-index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view('pages.dataexport.dataexport-add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Flash::success("Client was Successfully Updated");
        return response( $this->api->postForm( self::DATA_EXPORT_API_ENDPOINT , $request->all() ) );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response($this->api->getJSON(self::DATA_EXPORT_API_ENDPOINT, ['eid' => $id]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->view( 'pages.dataexport.dataexport-edit' );
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

    public function message(Request $request) {
        return response($this->api->getJSON(self::DATA_EXPORT_API_ENDPOINT, $request->all()));
    }
}