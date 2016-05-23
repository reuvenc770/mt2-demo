<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\Mt1Services\DataCleanseService;
use App\Services\MT1ApiService;
use Storage;
use Cache;

class DataCleanseController extends Controller
{
    const DATA_CLEANSE_API_ENDPOINT = 'dataexport_upd';
    protected $service;
    protected $api;

    public function __construct ( DataCleanseService $service , MT1ApiService $api ) {
        $this->service = $service;
        $this->api = $api;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        return response( $this->service->getAll( 
            $request->input( 'page' ) ,
            $request->input( 'count' )
        ) );
    }

    public function listAll () {
        return response()->view( 'pages.datacleanse.datacleanse-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view( 'pages.datacleanse.datacleanse-add' , [ 'dataExportFiles' => Storage::disk( 'dataExportFTP' )->files( 'Incoming' ) ] );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate( $request , [
            'pname' => 'required' ,
            'ConfirmEmail' => 'required' ,
            'aid' => 'required'
        ] );

        Cache::forget( 'datacleanse' );

        return response( $this->api->postForm(
            self::DATA_CLEANSE_API_ENDPOINT ,
            $request->all()
        ) );
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
