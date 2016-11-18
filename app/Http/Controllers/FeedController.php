<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\FeedEditRequest;
use App\Http\Requests\FeedFieldUpdateRequest;
use App\Services\ClientService;
use App\Services\FeedService;
use Cache;

class FeedController extends Controller
{

    protected $clientService;
    protected $feedService;

    public function __construct ( ClientService $clientService , FeedService $feedService ) {
        $this->clientService = $clientService;
        $this->feedService = $feedService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json( $this->feedService->getFeeds() );
    }

    /**
     *
     */
    public function listAll () {
        $countryList = $this->feedService->getCountries();

        return response()->view( 'bootstrap.pages.feed.feed-index' , [
            'countries' => ( !is_null( $countryList ) ? $countryList : [] ),
            'clients' => $this->clientService->get(),
            'clientTypes' => $this->feedService->getVerticals(),
            'feedTypes' => $this->feedService->getFeedTypes()
        ] );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countryList = $this->feedService->getCountries();

        return response()->view( 'bootstrap.pages.feed.feed-add' , [
            'hideName' => 'show' ,
            'countries' => ( !is_null( $countryList ) ? $countryList : [] ),
            'clients' => $this->clientService->get(),
            'clientTypes' => $this->feedService->getVerticals(),
            'feedTypes' => $this->feedService->getFeedTypes()
        ] );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FeedEditRequest $request)
    {
        Flash::success( 'Feed was successfully saved.' );

        $this->feedService->updateOrCreate( $request->all() );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->feedService->getFeed( $id );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $countryList = $this->feedService->getCountries() ?: [];

        return response()->view( 'bootstrap.pages.feed.feed-edit' , [
            'hideName' => 'hide' ,
            'countries' =>  $countryList,
            'clients' => $this->clientService->get(),
            'clientTypes' => $this->feedService->getVerticals(),
            'feedTypes' => $this->feedService->getFeedTypes()
        ] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FeedEditRequest $request, $id)
    {
        Flash::success( 'Feed was successfully updated.' );

        $this->feedService->updateOrCreate( $request->all() , $id );
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

    public function resetClientPassword($username) {

    }

    public function viewFieldOrder ( $id ) {
        return response()->view( 'bootstrap.pages.feed.feed-file-fields' , [
            "id" => $id ,
            "fields" => $this->feedService->getFeedFields( $id )
        ] );
    }

    public function storeFieldOrder ( FeedFieldUpdateRequest $request , $id ) {
        Flash::success( 'File Drop Field Order was successfully updated.' );

        $this->feedService->saveFieldOrder( $id , $request->all() );
    }

    public function searchSource ( Request $request ) {
        return response()->json( $this->feedService->getRecordCountForSource( $request->all() ) );
    }
}
