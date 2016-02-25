<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Cache;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\MT1ApiService;

class ClientGroupController extends Controller
{
    const CLIENT_GROUP_API_ENDPOINT = 'clientgroup';
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
        //
    }

    public function listAll () {
        return response()->view( 'pages.clientgroup.clientgroup-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view( 'pages.clientgroup.clientgroup-add' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Flash::success( 'Client Group was successfully created.' );

        Cache::forget( 'clientgroup' );

        return response( $this->api->postForm(
            self::CLIENT_GROUP_API_ENDPOINT ,
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->view( 'pages.clientgroup.clientgroup-update' );
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
        Flash::success( 'Client Group was successfully updated.' );

        Cache::forget( 'clientgroup' );

        return response( $this->api->postForm(
            self::CLIENT_GROUP_API_ENDPOINT ,
            $request->all()
        ) );
    }

    public function copy ( $id ) {
        Flash::success( 'Client Group was successfully copied.' );

        Cache::forget( 'clientgroup' );

        return response()->json( [
            "id" => $this->api->postForm(
                self::CLIENT_GROUP_API_ENDPOINT ,
                [ 'action' => 'copy', 'gid' => $id ]
            )
        ] );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id )
    {
        Flash::success( 'Client Group was successfully deleted.' );

        Cache::forget( 'clientgroup' );

        return response()->json( $this->api->postForm(
            self::CLIENT_GROUP_API_ENDPOINT ,
            [ 'action' => 'delete' , 'gid' => $id ] 
        ) );
    }
}
