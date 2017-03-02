<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\FeedGroupRequest;
use App\Services\FeedGroupService;

class FeedGroupController extends Controller
{
    protected $feedGroupService;

    public function __construct ( FeedGroupService $feedGroupService ) {
        $this->feedGroupService = $feedGroupService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //TODO SERVICE IS NO set and feedServiceGroup does not seem like the right one
        return response()->json( $this->service->getAll() );
    }

    public function paginateSearch ( Request $request ) {
        //TODO SERVICE IS NO set and feedServiceGroup does not seem like the right one

        $groupCollection = collect( $this->service->search( $request->input( 'query' ) ) );

        $queryChunk = $groupCollection->forPage( $request->input( 'page' ) , 20 );

        return response()->json( $queryChunk );
    }

    public function listAll () {
        return response()->view( 'pages.feedgroup.feedgroup-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view( 'pages.feedgroup.feedgroup-add' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\FeedGroupRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store( FeedGroupRequest $request)
    {
        Flash::success( 'Feed Group was successfully created.' );

        return response()->json( [ "id" => $this->saveFeedGroup( $request ) ] );
    }

    protected function saveFeedGroup ( $request ) {
        $id = $this->feedGroupService->updateOrCreate( $request->all() );

        $this->feedGroupService->updateFeeds( [
            'id' => $id ,
            'feeds' => $request->input( 'feeds' )
        ] );

        return $id;
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
        return response()->view( 'pages.feedgroup.feedgroup-update' , [
            'id' => $id ,
            'name' => $this->feedGroupService->getName( $id ) ,
            'feeds' => $this->feedGroupService->getFeeds( $id )
        ] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\FeedGroupRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update( FeedGroupRequest $request, $id)
    {
        Flash::success( 'Feed Group was successfully updated.' );

        $this->saveFeedGroup( $request );
    }

    public function copy ( $id ) {
        Flash::success( 'Feed Group was successfully copied.' );

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
        Flash::success( 'Feed Group was successfully deleted.' );

        return response()->json( $this->api->postForm(
            self::CLIENT_GROUP_API_ENDPOINT ,
            [ 'action' => 'delete' , 'gid' => $id ]
        ) );
    }
}
