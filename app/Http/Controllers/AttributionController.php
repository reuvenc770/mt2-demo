<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\MT1ApiService;
use App\Http\Requests\AttributionPostRequest;
class AttributionController extends Controller
{
    const ATTRIBUTION_BULK_UPLOAD_ENDPOINT ="upload_orange_attribution";
    const ATTRIBUTION_UPLOAD_ENDPOINT = "orange_attribution_add";
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
        return response( 'Unauthorized' , 401 );
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
    public function store(AttributionPostRequest $request)
    {
        return response( $this->api->postForm( self::ATTRIBUTION_UPLOAD_ENDPOINT , $request->all()) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulk(AttributionUploadRequest $request)
    {
        $filePath = null;
        if ($request->hasFile('upload_file')) {
            $rand = str_random(10);
            $fileName = "{$rand}.csv";
            $file = $request->file('upload_file')->move(storage_path("app/temp/"), $fileName);
            $filePath = $file->getPath();

        }
        return response( $this->api->postForm( self::ATTRIBUTION_BULK_UPLOAD_ENDPOINT , $request->all(), $filePath ) );
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
