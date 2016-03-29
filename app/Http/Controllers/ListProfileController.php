<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\MT1ApiService;
use Laracasts\Flash\Flash;
use App\Services\MT1Services\UniqueProfileService;
use Cache;

class ListProfileController extends Controller
{
    CONST LIST_PROFILE_API_ENDPOINT = 'profile_calc';
    CONST LIST_PROFILE_ACTION_API_ENDPOINT = 'profile_action';

    public $api;
    public $service;

    public function __construct ( MT1ApiService $api , UniqueProfileService $service ) {
        $this->api = $api;
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
        return response()->view( 'pages.listprofile.list-profile-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view( 'pages.listprofile.list-profile-add' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Flash::success( "List Profile was Successfully Saved" );

        Cache::tags('uniqueprofile')->flush();

        $versionString = ( $request->input( 'form_version' ) > 1 ? '_v' . $request->input( 'form_version' ) : '' );

        return response(  $this->api->postForm( self::LIST_PROFILE_API_ENDPOINT . $versionString , $request->all() ) )->header( 'Content-Type' , 'text/html' );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json( $this->service->getById( $id ) );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->view( 'pages.listprofile.list-profile-edit' );
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
        Flash::success( "List Profile was Successfully Updated" );

        Cache::tags('uniqueprofile')->flush();

        return response( $this->api->postForm( self::LIST_PROFILE_ACTION_API_ENDPOINT , $request->all() ) );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Flash::success( "List Profile was Successfully Deleted" );

        Cache::tags('uniqueprofile')->flush();

        return response( $this->api->postForm( self::LIST_PROFILE_ACTION_API_ENDPOINT , [ "action" => "delete" , "pid" => $id ] ) );
    }

    public function copy ( Request $request ) {
        Flash::success( "List Profile '" . $request->input( 'pname' ) . "' was Successfully Copied" );

        Cache::tags('uniqueprofile')->flush();

        return response( $this->api->postForm( self::LIST_PROFILE_ACTION_API_ENDPOINT , $request->all() ) );
    }

    public function isps ( $profileId ) {
        return response()->json(
            $this->service->getIspsByProfileId( $profileId )
        );
    }

    public function sources ( $profileId ) {
        return response()->json(
            $this->service->getSourcesByProfileId( $profileId )
        );
    }

    public function seeds ( $profileId ) {
        return response()->json(
            $this->service->getSeedsByProfileId( $profileId )
        );
    }

    public function zips ( $profileId ) {
        return response()->json(
            $this->service->getZipsByProfileId( $profileId )
        );
    }
}
