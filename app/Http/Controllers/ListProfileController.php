<?php

namespace App\Http\Controllers;

use App\Services\ListProfileService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\FeedRepo;
use App\Services\MT1Services\ClientStatsGroupingService;
use App\Services\MT1Services\ClientService;
use App\Services\MT1Services\CountryService;
use AdrianMejias\States\States;
use App\Services\DomainGroupService;
use App\Models\CakeVertical;
use App\Services\OfferService;

class ListProfileController extends Controller
{
    protected $listProfile;
    protected $feedRepo;
    protected $clientService;
    protected $mt1ClientService;
    protected $states;
    protected $ispService;
    protected $offerService;

    public function __construct (
        ListProfileService $listProfileService ,
        FeedRepo $feedRepo ,
        ClientStatsGroupingService $clientService ,
        ClientService $mt1ClientService ,
        CountryService $mt1CountryService ,
        States $states ,
        DomainGroupService $ispService ,
        OfferService $offerService
    ) {
        $this->listProfile = $listProfileService;
        $this->feedRepo = $feedRepo;
        $this->clientService = $clientService;
        $this->mt1ClientService = $mt1ClientService;
        $this->mt1CountryService = $mt1CountryService;
        $this->states = $states;
        $this->ispService = $ispService;
        $this->offerService = $offerService;
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
        return response()->view( 'pages.listprofile.list-profile-add' , $this->getFormFieldOptions() );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->view( 'pages.listprofile.list-profile-edit' , $this->getFormFieldOptions( [ 'id' => $id ] ) );
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    //USES LIST PROFILE DB NOT MT1 UNIQUE PROFILE
    public function listActive(){
        return response()->json(
            $this->listProfile->getActiveListProflies()
        );
    }

    protected function getFormFieldOptions ( $addOptions = [] ) {
        $feeds = $this->feedRepo->getFeeds()->keyBy( 'id' )->toArray();
        $clients = $this->clientService->getListGroups()->keyBy( 'value' )->toArray();

        $clientFeedMap = [];

        foreach ( $clients as $currentClientId => $currentClient ) {
            $clientFeedList = $this->mt1ClientService->getClientFeedsForListOwner( $currentClientId );

            foreach ( $clientFeedList as $currentFeedId ) {
                if ( !isset( $clientFeedMap[ $currentClientId ] ) ) {
                    $clientFeedMap[ $currentClientId ] = [];
                }

                $clientFeedMap[ $currentClientId ] []= $currentFeedId;
            }
        }

        return array_merge( [
            'feeds' => $feeds ,
            'clients' => $clients ,
            'clientFeedMap' => $clientFeedMap ,
            'countries' => $this->mt1CountryService->getAll() ,
            'states' => $this->states->all() ,
            'isps' => $this->ispService->getAll() ,
            'categories' => CakeVertical::all() ,
            'offers' => $this->offerService->all() 
        ] , $addOptions );
    }
}
